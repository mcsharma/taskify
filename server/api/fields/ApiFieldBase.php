<?hh // strict

<<__ConsistentConstruct>>
abstract class ApiFieldBase {

  private ?int $viewerID;
  private ?string $name;
  private ?NodeBase $parentNode;

  public function __construct() {
  }

  public function setName(string $name): this {
    $this->name = $name;
    return $this;
  }

  public function setParentNode(NodeBase $node): this {
    $this->parentNode = $node;
    return $this;
  }

  public function setViewerID(int $viewerID): this {
    $this->viewerID = $viewerID;
    return $this;
  }

  public function getViewerID(): int {
    $viewer_id = $this->viewerID;
    invariant($viewer_id !== null, 'viewer ID must have been set by now');
    return $viewer_id;
  }

  public function parentNode(): ?NodeBase {
    return $this->parentNode;
  }

  public function name(): ?string {
    return $this->name;
  }

  abstract public function genResult(): Awaitable<mixed>;
}
