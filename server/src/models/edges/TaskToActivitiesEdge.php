<?hh // strict


final class TaskToActivitiesEdge extends EdgeBase<Activity> {

  public function getEdgeType(): EdgeType {
    return EdgeType::TASK_TO_ACTIVITY;
  }

  public function getTargetNodeType(): classname<Activity> {
    return Activity::class;
  }
}
