<?hh // strict


final class ApiUserActivitiesEdge extends ApiEdge<Activity> {

  public function getEdgeClass(): classname<UserToActivitiesEdge> {
    return UserToActivitiesEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiActivityNode> {
    return ApiActivityNode::class;
  }
}
