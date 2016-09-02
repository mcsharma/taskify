<?hh // strict

enum EdgeType: int {
    USER_TO_OWNED_TASK = 1;
    TASK_TO_OWNER = 2;
    USER_TO_CREATED_TASK = 3;
    TASK_TO_CREATOR = 4;
}

abstract final class EdgeUtil {

  private static ImmMap<EdgeType, EdgeType> $inverseEdge = ImmMap {
    EdgeType::USER_TO_OWNED_TASK => EdgeType::TASK_TO_OWNER,
    EdgeType::TASK_TO_OWNER => EdgeType::USER_TO_OWNED_TASK,

    EdgeType::USER_TO_CREATED_TASK => EdgeType::TASK_TO_CREATOR,
    EdgeType::TASK_TO_CREATOR => EdgeType::USER_TO_CREATED_TASK,
  };

  public static function getInverse(EdgeType $edgeType): ?EdgeType {
    return self::$inverseEdge->contains($edgeType)
      ? self::$inverseEdge[$edgeType]
      : null;
  }
}
