<?hh // strict

require_once('api/fields/ApiEdge.php');
require_once('models/edges/UserToCreatedTasksEdge.php');
require_once('api/nodes/ApiTaskNode.php');

final class ApiUserCreatedTasksEdge extends ApiEdge<Task> {

  public function getEdgeClass(): classname<UserToCreatedTasksEdge> {
    return UserToCreatedTasksEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiTaskNode> {
    return ApiTaskNode::class;
  }
}
