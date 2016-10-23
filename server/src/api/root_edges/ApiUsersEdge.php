<?hh // strict


final class ApiUsersEdge extends ApiRootEdgeBase<User> {

  public function getRootEdgeClass(): classname<UsersEdge> {
    return UsersEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiUserNode> {
    return ApiUserNode::class;
  }
}
