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
  private ?string $oldDescription;
  private ?string $newDescription;
  private ?TaskStatus $newStatus;
  private ?Priority $oldPriority;
  private ?Priority $newPriority;

  public function __construct(int $viewerID, Map<string, string> $node) {
    parent::__construct($viewerID, $node);
    $data = json_decode($node['data'], true);
    $this->changedField = TaskField::assert($data['changed']);

    $this->actorID = (int)$data['actor_id'];
    $this->taskID = (int)$data['task_id'];

    $this->oldTitle = idx($data, 'old_title');
    $this->newTitle = idx($data, 'new_title');

    $this->oldDescription = idx($data, 'old_description') ?: null;
    $this->newDescription = idx($data, 'new_description') ?: null;

    $this->newStatus = TaskStatus::coerce(idx($data, 'new_status'));

    $this->oldPriority = Priority::coerce(idx($data, 'old_priority'));
    $this->newPriority = Priority::coerce(idx($data, 'new_priority'));
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

  public function getOldDescription(): ?string {
    return $this->oldDescription;
  }

  public function getNewDescription(): ?string {
    return $this->newDescription;
  }

  public function getNewStatus(): ?TaskStatus {
    return $this->newStatus;
  }

  public function getOldPriority(): ?Priority {
    return $this->oldPriority;
  }

  public function getNewPriority(): ?Priority {
    return $this->newPriority;
  }
}
