<?hh // strict


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
  private ?Vector<int> $addedTagIDs;
  private ?Vector<int> $removedTagIDs;
  private ?Vector<int> $addedSubscriberIDs;
  private ?Vector<int> $removedSubscriberIDs;
  private ?int $oldOwnerID;
  private ?int $newOwnerID;

  public function __construct(int $viewerID, Map<string, string> $node) {
    parent::__construct($viewerID, $node);
    $data = json_decode($node['data'], true);
    $this->changedField = TaskField::assert($data['changed']);

    $this->actorID = (int)$data['actor_id'];
    $this->taskID = (int)$data['task_id'];

    if ($this->changedField === TaskField::TITLE) {
      $this->oldTitle = idx($data, 'old_title') ?: null;
      $this->newTitle = $data['new_title'];
    }

    if ($this->changedField === TaskField::DESCRIPTION) {
      $this->oldDescription = idx($data, 'old_description') ?: null;
      $this->newDescription = $data['new_description'];
    }

    if ($this->changedField === TaskField::STATUS) {
      $this->newStatus = TaskStatus::assert($data['new_status']);
    }

    if ($this->changedField === TaskField::PRIORITY) {
      $this->oldPriority = Priority::coerce($data['old_priority']);
      $this->newPriority = Priority::assert($data['new_priority']);
    }

    if ($this->changedField === TaskField::OWNER) {
      $this->oldOwnerID = idx($data, 'old_owner_id') ?: null;
      $this->newOwnerID = idx($data, 'new_owner_id') ?: null;
    }

    if ($this->changedField === TaskField::TAGS) {
      $this->addedTagIDs = new Vector(idx($data, 'added', array()));
      $this->removedTagIDs = new Vector(idx($data, 'removed', array()));
    }
    if ($this->changedField === TaskField::SUBSCRIBERS) {
      $this->addedSubscriberIDs = new Vector(idx($data, 'added', array()));
      $this->removedSubscriberIDs = new Vector(idx($data, 'removed', array()));
    }
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

  public function getAddedTagIDs(): ?Vector<int> {
    return $this->addedTagIDs;
  }

  public function getRemovedTagIDs(): ?Vector<int> {
    return $this->removedTagIDs;
  }

  public function getAddedSubscriberIDs(): ?Vector<int> {
    return $this->addedSubscriberIDs;
  }

  public function getRemovedSubscriberIDs(): ?Vector<int> {
    return $this->removedSubscriberIDs;
  }

  public function getOldOwnerID(): ?int {
    return $this->oldOwnerID;
  }

  public function getNewOwnerID(): ?int {
    return $this->newOwnerID;
  }
 }
