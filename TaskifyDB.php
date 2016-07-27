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
}
