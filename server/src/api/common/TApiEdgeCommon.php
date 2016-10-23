<?hh // strict

trait TApiEdgeCommon<+T as NodeBase> {

  private ImmMap<string, mixed> $fieldsTree = ImmMap {};
  private ImmMap<string, mixed> $params = ImmMap {};

  public function setFieldsTree(ImmMap<string, mixed> $fieldsTree): this {
    $this->fieldsTree = $fieldsTree;
    return $this;
  }

  public function setParams(ImmMap<string, string> $params): this {
    $this->params = $params;
    return $this;
  }

  abstract public function getTargetNodeClass(): classname<ApiNode<T>>;

  private async function genNodesToApiResult(ConstVector<T> $nodes): Awaitable<Map<string, mixed>> {
    $api_node_class = $this->getTargetNodeClass();
    $nodes_data = Vector {};
    foreach ($nodes as $node) {
      $api_node = new $api_node_class();
      $res = await $api_node
        ->setViewerID($this->getViewerID())
        ->setRawFieldValue($node)
        ->setFieldsTree($this->fieldsTree)
        ->genResult();
      $nodes_data[] = $res;
    }
    return Map {
      'total_count' => $nodes_data->count(),
      'nodes' => $nodes_data,
    };
  }

  // TODO this should go away after trait for viewer ID
  abstract public function getViewerID(): int;
}
