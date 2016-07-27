<?hh // strict

<<__ConsistentConstruct>>
abstract class ApiFieldBase {

  private ?string $name;
  private ?NodeBase $parentNode;

  public function setName(string $name): this {
    $this->name = $name;
    return $this;
  }

  public function setParentNode(NodeBase $node): this {
    $this->parentNode = $node;
    return $this;
  }

  public function parentNode(): ?NodeBase {
    return $this->parentNode;
  }

  public function name(): ?string {
    return $this->name;
  }

  abstract public function genResult(): Awaitable<mixed>;
}
