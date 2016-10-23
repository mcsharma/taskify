<?hh // strict

require_once('api/fields/ApiEdge.php');
require_once('models/Activity.php');
require_once('models/edges/TaskToActivitiesEdge.php');

final class ApiTaskActivitiesEdge extends ApiEdge<Activity> {

  public function getEdgeClass(): classname<TaskToActivitiesEdge> {
    return TaskToActivitiesEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiActivityNode> {
    return ApiActivityNode::class;
  }
}
