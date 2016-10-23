<?hh // strict


final class ApiTaskActivitiesEdge extends ApiEdge<Activity> {

  public function getEdgeClass(): classname<TaskToActivitiesEdge> {
    return TaskToActivitiesEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiActivityNode> {
    return ApiActivityNode::class;
  }
}
