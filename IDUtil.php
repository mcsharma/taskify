<?hh // strict

require('NodeType.php');

final class IDUtil {

    private static Map<NodeType, string> $nodeTypeToTable = Map {
        NodeType::USER => 'user',
        NodeType::TASK => 'task',
    };

    public static function idToTable(int $id): string {
        return self::$nodeTypeToTable[self::idToType($id)];
    }

    public static function idToType(int $id): NodeType {
        static $nodeTypeToRange = Map {
            NodeType::USER => tuple(1, 100000000000000),
            NodeType::TASK => tuple(100000000000001, 200000000000000),
        };

        foreach ($nodeTypeToRange as $node_type => $range) {
            if ($id >= $range[0] && $id <= $range[1]) {
                return $node_type;
            }
        }
        return NodeType::TASK;
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
