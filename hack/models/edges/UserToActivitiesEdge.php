<?hh // strict

require_once('EdgeBase.php');

final class UserToActivitiesEdge extends EdgeBase<Activity> {

  public function getEdgeType(): EdgeType {
    return EdgeType::ACTOR_TO_ACTIVITY;
  }

  public function getTargetNodeType(): classname<Activity> {
    return Activity::class;
  }
}
