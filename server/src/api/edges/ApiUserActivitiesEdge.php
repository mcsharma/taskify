<?hh // strict

require_once('api/fields/ApiEdge.php');
require_once('models/Activity.php');
require_once('models/edges/UserToActivitiesEdge.php');

final class ApiUserActivitiesEdge extends ApiEdge<Activity> {

  public function getEdgeClass(): classname<UserToActivitiesEdge> {
    return UserToActivitiesEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiActivityNode> {
    return ApiActivityNode::class;
  }
}
