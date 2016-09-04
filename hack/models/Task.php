<?hh // strict

require_once ('NodeBase.php');

final class Task extends NodeBase {

  private string $title;
  private ?string $description;
  private ?int $ownerID;
  private Priority $priority;

  public function __construct(Map<string, string> $node) {
    parent::__construct($node);
    $data = json_decode($node['data'], true /*return array instead*/);
    $this->title = $data['title'];
    $this->description = array_key_exists('description', $data) ? $data['description'] : null;
    $this->ownerID = array_key_exists('owner_id', $data) ? $data['owner_id']: null;
    $this->priority = array_key_exists('priority', $data) ? Priority::assert($data['priority']) : Priority::UNSPECIFIED;
  }

  public function getTitle(): string {
    return $this->title;
  }

  public function getDescription(): ?string {
    return $this->description;
  }

  public function getOwnerID(): ?int {
    return $this->ownerID;
  }

  public function getPriority(): Priority {
    return $this->priority;
  }
}
