<?hh // strict


final class ApiUserCreatedTasksEdge extends ApiEdge<Task> {

  public function getEdgeClass(): classname<UserToCreatedTasksEdge> {
    return UserToCreatedTasksEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiTaskNode> {
    return ApiTaskNode::class;
  }
}
