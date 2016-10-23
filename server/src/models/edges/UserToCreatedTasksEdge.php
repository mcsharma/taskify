<?hh // strict


final class UserToCreatedTasksEdge extends EdgeBase<Task> {

  public function getEdgeType(): EdgeType {
    return EdgeType::USER_TO_CREATED_TASK;
  }

  public function getTargetNodeType(): classname<Task> {
    return Task::class;
  }
}
