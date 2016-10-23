<?hh // strict

require_once('api/fields/ApiEdge.php');
require_once('models/edges/TaskToSubscribersEdge.php');
require_once('models/User.php');
require_once('api/nodes/ApiUserNode.php');

final class ApiTaskSubscribersEdge extends ApiEdge<User> {

  public function getEdgeClass(): classname<TaskToSubscribersEdge> {
    return TaskToSubscribersEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiUserNode> {
    return ApiUserNode::class;
  }
}
