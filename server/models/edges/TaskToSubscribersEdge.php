<?hh // strict

require_once('EdgeBase.php');

final class TaskToSubscribersEdge extends EdgeBase<User> {

  public function getEdgeType(): EdgeType {
    return EdgeType::TASK_TO_SUBSCRIBER;
  }

  public function getTargetNodeType(): classname<User> {
    return User::class;
  }
}
