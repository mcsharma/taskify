<?hh // strict

require_once('hack/api/fields/ApiEdge.php');
require_once('hack/models/Activity.php');
require_once('hack/models/edges/TaskToActivitiesEdge.php');

final class ApiTaskActivitiesEdge extends ApiEdge<Activity> {

  public function getEdgeClass(): classname<TaskToActivitiesEdge> {
    return TaskToActivitiesEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiActivityNode> {
    return ApiActivityNode::class;
  }
}
