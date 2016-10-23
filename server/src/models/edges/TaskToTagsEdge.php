<?hh // strict

require_once('EdgeBase.php');

final class TaskToTagsEdge extends EdgeBase<Tag> {

  public function getEdgeType(): EdgeType {
    return EdgeType::TASK_TO_TAG;
  }

  public function getTargetNodeType(): classname<Tag> {
    return Tag::class;
  }
}
