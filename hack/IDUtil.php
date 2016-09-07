<?hh // strict

require('NodeType.php');
require_once ('api/nodes/ApiTagNode.php');
require_once ('api/nodes/ApiUserNode.php');
require_once ('api/nodes/ApiTaskNode.php');
require_once ('api/nodes/ApiActivityNode.php');
require_once ('models/Activity.php');

final class IDUtil {

    private static Map<NodeType, string> $nodeTypeToTable = Map {
        NodeType::USER => 'user',
        NodeType::TASK => 'task',
        NodeType::TAG => 'tag',
        NodeType::ACTIVITY => 'activity',
    };

    private static Map<NodeType, string> $nodeTypeToApiTypename = Map {
        NodeType::USER => 'User',
        NodeType::TASK => 'Task',
        NodeType::TAG => 'Tag',
        NodeType::ACTIVITY => 'Activity',
    };

    private static Map<NodeType, (int, int)> $nodeTypeToRange = Map {
        NodeType::USER => tuple(1, 100000000000000),
        NodeType::TASK => tuple(100000000000001, 200000000000000),
        NodeType::TAG => tuple(200000000000001, 300000000000000),
        NodeType::ACTIVITY => tuple(300000000000001, 400000000000000),
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
        foreach (self::$nodeTypeToRange as $node_type => $range) {
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

    public static function idToNodeLoaderClass(int $id): classname<NodeBase> {
      $type = self::idToType($id);
      switch ($type) {
        case NodeType::USER:
          return User::class;
        case NodeType::TASK:
          return Task::class;
        case NodeType::TAG:
          return Tag::class;
        case NodeType::ACTIVITY:
          return Activity::class;
      }
    }

    public static function nodeClassToApiNodeClass(
      classname<NodeBase> $node_class,
    ): classname<ApiNode<NodeBase>> {
      switch ($node_class) {
        case User::class:
          return ApiUserNode::class;
        case Task::class:
          return ApiTaskNode::class;
        case Tag::class:
          return ApiTagNode::class;
        case Activity::class:
          return ApiActivityNode::class;
      }
    }
}
