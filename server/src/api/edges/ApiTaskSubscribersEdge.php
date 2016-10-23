<?hh // strict


final class ApiTaskSubscribersEdge extends ApiEdge<User> {

  public function getEdgeClass(): classname<TaskToSubscribersEdge> {
    return TaskToSubscribersEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiUserNode> {
    return ApiUserNode::class;
  }
}
