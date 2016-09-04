<?hh // strict

require_once('hack/api/fields/ApiEdge.php');
require_once('hack/models/edges/UserToTasksEdge.php');
require_once('hack/api/nodes/ApiTaskNode.php');

final class ApiUserTasksEdge extends ApiEdge<Task> {

  public function getEdgeClass(): classname<UserToTasksEdge> {
    return UserToTasksEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiTaskNode> {
    return ApiTaskNode::class;
  }
}
