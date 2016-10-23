<?hh // strict

require_once ('api/root_edges/ApiRootEdgeBase.php');
require_once ('models/root_edges/UsersEdge.php');

final class ApiUsersEdge extends ApiRootEdgeBase<User> {

  public function getRootEdgeClass(): classname<UsersEdge> {
    return UsersEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiUserNode> {
    return ApiUserNode::class;
  }
}
