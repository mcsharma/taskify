<?hh // strict

require_once('metadata/TaskMetadata.php');

final class Activity extends NodeBase {

  private TaskField $changedField;

  // Always Present
  private int $actorID;
  private int $taskID;

  // Change specific
  private ?string $oldTitle;
  private ?string $newTitle;

  public function __construct(Map<string, string> $node) {
    parent::__construct($node);
    $data = json_decode($node['data'], true);
    $this->changedField = TaskField::assert($data['changed']);

    $this->actorID = (int)$data['actor_id'];
    $this->taskID = (int)$data['task_id'];

    $this->oldTitle = idx($data, 'old_title');
    $this->newTitle = idx($data, 'new_title');
  }

  public function getChangedField(): TaskField {
    return $this->changedField;
  }

  public function getActorID(): int {
    return $this->actorID;
  }

  public function getTaskID(): int {
    return $this->taskID;
  }

  public function getOldTitle(): ?string {
    return $this->oldTitle;
  }

  public function getNewTitle(): ?string {
    return $this->newTitle;
  }
}
