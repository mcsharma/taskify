<?hh // strict

require_once('hack/api/fields/ApiEdge.php');
require_once('hack/models/Activity.php');
require_once('hack/models/edges/UserToActivitiesEdge.php');

final class ApiUserActivitiesEdge extends ApiEdge<Activity> {

  public function getEdgeClass(): classname<UserToActivitiesEdge> {
    return UserToActivitiesEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiActivityNode> {
    return ApiActivityNode::class;
  }
}
