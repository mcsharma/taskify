<?hh // strict

require('NodeType.php');

final class IDUtil {

    private static Map<NodeType, string> $nodeTypeToTable = Map {
        NodeType::USER => 'user',
        NodeType::TASK => 'task',
    };

    private static Map<NodeType, string> $nodeTypeToApiTypename = Map {
        NodeType::USER => 'User',
        NodeType::TASK => 'Task',
    };

    public static function idToTable(int $id): string {
        return self::typeToTable(self::idToType($id));
    }

    public static function idToApiTypename(int $id): string {
        return self::typeToApiTypename(self::idToType($id));
    }

    public static function typeToTable(NodeType $type): string {
      return self::$nodeTypeToTable[$type];
    }

    public static function typeToApiTypename(NodeType $type): string {
      return self::$nodeTypeToApiTypename[$type];
    }

    public static function idToTypeNullable(int $id): ?NodeType {
        static $nodeTypeToRange = Map {
            NodeType::USER => tuple(1, 100000000000000),
            NodeType::TASK => tuple(100000000000001, 200000000000000),
        };

        foreach ($nodeTypeToRange as $node_type => $range) {
            if ($id >= $range[0] && $id <= $range[1]) {
                return $node_type;
            }
        }
        return null;
    }

    public static function idToType(int $id): NodeType {
      $type = self::idToTypeNullable($id);
      invariant($type !== null, 'expected a valid ID');
      return $type;
    }

    public static function isValidID(int $id): bool {
      return self::idToTypeNullable($id) !== null;
    }

    public static function idToNodeClass(int $id): classname<NodeBase> {
      $type = self::idToType($id);
      switch ($type) {
        case NodeType::USER:
          return User::class;
        case NodeType::TASK:
          return Task::class;
      }
    }
}
