<?hh


final class ApiNodeListField<T as NodeBase> extends ApiScalarField {

  private ImmMap<string, mixed> $fieldsTree = ImmMap {};

  public function __construct(
    string $method,
    private classname<ApiNode<T>> $nodeClass,
  ) {
    $this->setMethod($method);
  }

  public function setFieldsTree(ImmMap<string, mixed> $fieldsTree): this {
    $this->fieldsTree = $fieldsTree;
    return $this;
  }

  final public async function genResult(): Awaitable<?Vector<Map<string, mixed>>> {
    $items = $this->getRawFieldValue();
    if ($items === null) {
      return null;
    }
    invariant($items instanceof Traversable, 'Must be a traversable');
    $result = Vector {};
    foreach ($items as $item) {
      $api_node_class = $this->nodeClass;
      $res = await (new $api_node_class())
        ->setViewerID($this->getViewerID())
        ->setRawFieldValue($item)
        ->setFieldsTree($this->fieldsTree)
        ->genResult();
      if ($res !== null) {
        $result[] = $res;
      }
    }
    return $result;
  }
}
