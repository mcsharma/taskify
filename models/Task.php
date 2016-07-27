<?hh // strict

require_once ('NodeBase.php');

final class Task extends NodeBase {

  private string $title;
  private string $description;
  private int $ownerID;

  public function __construct(Map<string, string> $node) {
    parent::__construct($node);
    $data = json_decode($node['data']);

    $this->title = $data->title;
    $this->description = $data->description;
    $this->ownerID = $data->owner_id;
  }

  public function getTitle(): string {
    return $this->title;
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function getOwnerID(): int {
    return $this->ownerID;
  }
}
