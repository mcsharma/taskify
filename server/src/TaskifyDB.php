<?hh // strict

require_once('IDUtil.php');
require_once('NodeType.php');
require_once('EdgeType.php');
require_once('ServerConfig.php');

// Note: There seems to be a problem with queryf() function. It is
// apparently crashing hhvm and with stacktrace provided. That's why
// query() is used everywhere in this file with escapeString() explicitly called.
final class TaskifyDB {

    const int MAX_FETCH_LIMIT = 10000;

    private static async function genConnection(): Awaitable<\AsyncMysqlConnection> {
        // Get a connection pool with default options
        $pool = new \AsyncMysqlConnectionPool(array());
        return await $pool->connect(
          ServerConfig::getDBHost(),
          3306,
          'taskify',
          'root',
          ServerConfig::getDBPassword(),
        );
    }

    public static async function genNode(int $id): Awaitable<Map<string, string>> {
        $table = IDUtil::idToTable($id);
        $conn = await self::genConnection();
        // Note: There seems to be a problem with queryf() function. It is
        // apparently crashing hhvm and no stacktrace provided. That's why
        // queryf is used everywhere.
        $result = await $conn->query(sprintf(
          "SELECT * FROM %s WHERE id = %d AND is_deleted = 0",
          $table,
          $id,
        ));
        // There shouldn't be more than one row returned for one user id
        invariant($result->numRows() === 1, "error fetching %d one row exactly", $id);
        // A vector of vector objects holding the string values of each column
        // in the query
        return $result->mapRows()[0];
    }

    public static async function genNodesForType(
      NodeType $nodeType,
      int $limit = self::MAX_FETCH_LIMIT,
    ): Awaitable<Vector<Map<string, string>>> {
        $table = IDUtil::typeToTable($nodeType);
        $conn = await self::genConnection();
        $limit = min($limit, self::MAX_FETCH_LIMIT);
        $result = await $conn->query(sprintf("SELECT * from %s WHERE is_deleted = 0 LIMIT %d", $table, $limit));
        // A vector of vector objects holding the string values of each column
        // in the query

        return $result->mapRows();
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
      $result = await $conn->query(sprintf(
        "SELECT id2, created_time, updated_time, data FROM edge WHERE is_deleted = 0 AND type = %d AND id1 = %d AND id2 = %d",
        $edgeType,
        $id1,
        $id2,
      ));
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
      $response = await $conn->query(sprintf(
        'SELECT COUNT(1) FROM edge WHERE is_deleted = 0 AND id1 = %d AND id2 = %d AND type = %d',
        $id1,
        $id2,
        (int)$edgeType,
      ));
      return $response->vectorRows()[0][0] > 0;
    }

    public static async function genEdgesForType(
      int $id1,
      EdgeType $edgeType,
    ): Awaitable<Vector<Map<string, string>>> {
      $conn = await self::genConnection();
      $result = await $conn->query(sprintf(
        'SELECT id2, created_time, updated_time, data from edge WHERE is_deleted = 0 AND type = %d AND id1 = %d',
          $edgeType,
          $id1
      ));
      return $result->mapRows();
    }

    public static async function genCreateNode(
      NodeType $node_type,
      Map<string, mixed> $fields,
    ): Awaitable<int> {
      $conn = await self::genConnection();
      $table = IDUtil::typeToTable($node_type);
      $data = json_encode($fields);
      $q = sprintf("INSERT INTO %s (data) VALUES ('%s')", $table, $conn->escapeString($data));
      $res = await $conn->query($q);
      return $res->lastInsertId();
    }


    public static async function genUpdateNode(
      int $node_id,
      Map<string, mixed> $fields,
    ): Awaitable<void> {
      if ($fields->count() === 0) {
        return;
      }
      $conn = await self::genConnection();
      $table = IDUtil::idToTable($node_id);
      $update_strings = Vector {};
      foreach ($fields as $field => $value) {
        $key_str = sprintf('"$.%s"', $field);
        $value_str = "";
        if (is_string($value)) {
          $value_str = sprintf('"%s"', $conn->escapeString($value));
        } else if (is_int($value)) {
          $value_str = (string)$value;
        } else if (is_array($value) && array_values($value) === $value) {
          array_map(
            $val ==> is_string($val) ? $conn->escapeString($val) : $val,
            $value,
          );
          $value_str = sprintf("JSON_ARRAY(%s)", substr(json_encode($value), 1, -1));
        } else  {
          invariant_violation('Unimplemented field type found in in genUpdateNode');
        }
        $update_strings[] = $key_str.", ".$value_str;
      }
      $update_string = implode(', ', $update_strings);
      $q = sprintf(
        "UPDATE %s SET data = JSON_SET(data, %s) WHERE id = %d",
        $table,
        $update_string,
        $node_id,
      );
      $res = await $conn->query($q);
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

      $conn = await self::genConnection();
      $escaped_json_data = $conn->escapeString(json_encode($data));
      if ($inverse_type !== null) {
        // TODO add ON DUPLiCATE kEY UPDATE so that we dont' create duplicaete
        // rows
        await $conn->query(sprintf(
          "INSERT INTO edge (id1, id2, type, data) VALUES (%d, %d, %d, '%s'), (%d, %d, %d, '%s')",
          $id1, $id2, (int)$edge_type, $escaped_json_data,
          $id2, $id1, (int)$inverse_type, $escaped_json_data
        ));
      } else {
        await $conn->query(sprintf(
          "INSERT INTO edge (id1, id2, type, data) VALUES (%d, %d, %d, '%s')",
          $id1, $id2, (int)$edge_type, $escaped_json_data,
        ));
      }
    }

    public static async function genDeleteEdge(
      EdgeType $edge_type,
      int $id1,
      int $id2,
    ): Awaitable<void> {
      $edge_exists = await self::genEdgeExists($id1, $id2, $edge_type);
      if (!$edge_exists) {
        throw new Exception('Edge already deleted');
      }
      $conn = await self::genConnection();
      $inverse_type = EdgeUtil::getInverse($edge_type);
      if ($inverse_type !== null) {
        await $conn->query(sprintf(
          "UPDATE edge SET is_deleted = 1 WHERE (type = %d AND id1 = %d AND id2 = %d) OR (type = %d AND id1 = %d AND id2 = %d)",
          $edge_type, $id1, $id2,
          $inverse_type, $id2, $id1,
        ));
      } else {
        await $conn->query(sprintf(
          "UPDATE edge SET is_deleted = 1 WHERE type = %d AND id1 = %d AND id2 = %d",
          $edge_type, $id1, $id2,
        ));
      }
    }

    /**
     * Returns data from 'hash' table, for a given hash_type and key
     */
     // TODO convert hash type to enum
    public static async function genHashValue(
      string $hash_type,
      string $key,
    ): Awaitable<?Map<string, mixed>> {
      $conn = await self::genConnection();
      $result = await $conn->query(sprintf(
        "SELECT value from hash WHERE type = '%s' AND `key` = '%s'",
        $conn->escapeString($hash_type),
        $conn->escapeString($key),
      ));
      // There shouldn't be more than one row returned for one user id
      invariant($result->numRows() <= 1, 'Must be at most 1 row');
      if ($result->numRows() === 0) {
        return null;
      }
      // A vector of vector objects holding the string values of each column
      // in the query
      return new Map(json_decode($result->mapRows()[0]['value'], true));
    }

    public static async function genSetHash(
      string $has_type,
      string $key, Map<string, mixed> $value,
    ): Awaitable<void> {
      $conn = await self::genConnection();
      $encoded_value = json_encode($value);
      await $conn->query(sprintf(
        "INSERT INTO hash (type, `key`, value) VALUES('%s', '%s', '%s') ON DUPLICATE KEY UPDATE value='%s'",
        $conn->escapeString($has_type),
        $conn->escapeString($key),
        $conn->escapeString($encoded_value),
        $conn->escapeString($encoded_value),
      ));
    }
}
