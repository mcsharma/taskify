<?hh // strict

require('EdgeBase.php');

final class UserToTasksEdge extends EdgeBase<Task> {

  public function getEdgeType(): EdgeType {
    return EdgeType::USER_TO_OWNED_TASK;
  }

  public function getTargetNodeType(): classname<Task> {
    return Task::class;
  }
}
