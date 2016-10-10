<?hh // strict

abstract class ApiFieldBase {

  private ?int $viewerID;
  // name of the field, this is mostly for the purpose of error reporting.
  private ?string $name;

  public function setName(string $name): this {
    $this->name = $name;
    return $this;
  }

  public function name(): ?string {
    return $this->name;
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

  abstract public function genResult(): Awaitable<mixed>;
}
