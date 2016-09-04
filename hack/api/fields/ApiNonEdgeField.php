<?hh

require_once('ApiFieldBase.php');

class ApiNonEdgeField extends ApiFieldBase {

  private ?string $methodName;

  public async function genResult(): Awaitable<mixed> {
    $parent = $this->parentNode();
    $method = $this->methodName;
    $node = $method !== null ? $parent->$method() : $parent;
    if ($node instanceof Awaitable) {
      $node = await $node;
    }
    return $node;
  }

  public function setMethod(string $methodName): this {
    $this->methodName = $methodName;
    return $this;
  }
}
