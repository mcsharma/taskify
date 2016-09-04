<?hh // strict

require_once('hack/api/fields/ApiEdge.php');
require_once('hack/models/edges/TaskToSubscribersEdge.php');
require_once('hack/models/User.php');
require_once('hack/api/nodes/ApiUserNode.php');

final class ApiTaskSubscribersEdge extends ApiEdge<User> {

  public function getEdgeClass(): classname<TaskToSubscribersEdge> {
    return TaskToSubscribersEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiUserNode> {
    return ApiUserNode::class;
  }
}
