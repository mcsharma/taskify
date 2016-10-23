<?hh // strict

require_once('api/fields/ApiEdge.php');
require_once('models/edges/UserToTasksEdge.php');
require_once('api/nodes/ApiTaskNode.php');

final class ApiUserTasksEdge extends ApiEdge<Task> {

  public function getEdgeClass(): classname<UserToTasksEdge> {
    return UserToTasksEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiTaskNode> {
    return ApiTaskNode::class;
  }
}
