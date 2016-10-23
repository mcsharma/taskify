<?hh // strict


final class ApiUserTasksEdge extends ApiEdge<Task> {

  public function getEdgeClass(): classname<UserToTasksEdge> {
    return UserToTasksEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiTaskNode> {
    return ApiTaskNode::class;
  }
}
