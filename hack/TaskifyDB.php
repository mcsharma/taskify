<?hh // strict

require('IDUtil.php');
require('NodeType.php');
require('EdgeType.php');

final class TaskifyDB {

    private static async function genConnection(): Awaitable<\AsyncMysqlConnection> {
        // Get a connection pool with default options
        $pool = new \AsyncMysqlConnectionPool(array());
        return await $pool->connect('localhost', 3306, 'taskify', 'root', '');
    }

    public static async function genNode(int $id): Awaitable<Map<string, string>> {
        $table = IDUtil::idToTable($id);
        $conn = await self::genConnection();
        $result = await $conn->query('SELECT * from '.$table.' WHERE id = '.$id);
        // There shouldn't be more than one row returned for one user id
        invariant($result->numRows() === 1, 'one row exactly');
        // A vector of vector objects holding the string values of each column
        // in the query
        return $result->mapRows()[0];
    }

    /**
     * Returns null if edge doesn't exist, otherwise returns Map containting
     * edge data.
     */
    public static async function genEdge(
      int $id1,
      int $id2,
      int $edgeType,
    ): Awaitable<?Map<string, string>> {
      $conn = await self::genConnection();
      $result = await $conn->query(
        'SELECT id2, created_time, updated_time, data from edge WHERE type = '.
          $edgeType.
          ' AND id1 = '.
          $id1.
          ' AND id2 = '.
          $id2
      );
      invariant($result->numRows() <= 1, "Shouldn't be more than one edge");
      if ($result->numRows() === 1) {
        // edge exists
        return $result->mapRows()[0];
      }
      return null;
    }

    public static async function genEdgeExists(
      int $id1,
      int $id2,
      EdgeType $edgeType,
    ): Awaitable<bool> {
      $conn = await self::genConnection();
      $response = await $conn->queryf(
        'SELECT COUNT(1) FROM edge WHERE id1 = %d AND id2 = %d AND type = %d',
        $id1,
        $id2,
        (int)$edgeType,
      );
      return $response->vectorRows()[0][0] > 0;
    }

    public static async function genEdgesForType(
      int $id1,
      EdgeType $edgeType,
    ): Awaitable<Vector<Map<string, string>>> {
      $conn = await self::genConnection();
      $result = await $conn->query(
        'SELECT id2, created_time, updated_time, data from edge WHERE type = '.
          $edgeType.
          ' AND id1 = '.
          $id1
      );
      return $result->mapRows();
    }

    public static async function genCreateNode(
      NodeType $node_type,
      Map<string, mixed> $fields,
    ): Awaitable<int> {
      $conn = await self::genConnection();
      $table = IDUtil::typeToTable($node_type);
      $data = json_encode($fields);
      $res = await $conn->queryf(
        "INSERT INTO %T (data) VALUES (%s)",
        $table,
        $data,
      );
      return $res->lastInsertId();
    }

    public static async function genCreateEdge(
      EdgeType $edge_type,
      int $id1,
      int $id2,
      ?Map<string, mixed> $data = null,
    ): Awaitable<void> {
      $inverse_type = EdgeUtil::getInverse($edge_type);
      $gens = Vector {
        self::genEdgeExists($id1, $id2, $edge_type)
      };
      if ($inverse_type !== null) {
        $gens[] = self::genEdgeExists($id2, $id1, $inverse_type);
      }
      $results = await \HH\Asio\v($gens);
      if ($results[0] || ($inverse_type !== null && $results[1])) {
        throw new Exception('Edge already exists');
      }

      $json_data = json_encode($data);
      $conn = await self::genConnection();
      if ($inverse_type !== null) {
        await $conn->queryf(
          'INSERT INTO edge (id1, id2, type, data) VALUES (%d, %d, %d, %s), (%d, %d, %d, %s)',
          $id1, $id2, (int)$edge_type, $json_data,
          $id2, $id1, (int)$inverse_type, $json_data
        );
      } else {
        await $conn->queryf(
          'INSERT INTO edge (id1, id2, type) VALUES (%d, %d, %d, %s)',
          $id1, $id2, (int)$edge_type, $json_data
        );
      }
    }
}
