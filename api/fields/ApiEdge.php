<?hh // strict

// require_once('ApiFieldBase.php');
// require_once('api/nodes/ApiNodeBase.php');
//
abstract class ApiEdge<T as NodeBase> extends ApiFieldBase {

  private ImmMap<string, mixed> $fieldsTree = ImmMap {};

  abstract public function getEdgeClass(): classname<EdgeBase<T>>;

  abstract public function getTargetNodeClass(): classname<ApiNode<T>>;

  public async function genResult(): Awaitable<?Map<string, mixed>> {
    $parent_node = $this->parentNode();
    invariant($parent_node !== null, 'parent must have been set');
    $edge_class = $this->getEdgeClass();
    $nodes = await (new $edge_class($parent_node->getID()))->genNodes();
    $api_node_class = $this->getTargetNodeClass();
    $nodes_data = Vector {};
    foreach ($nodes as $node) {
      $api_node = new $api_node_class();
      $res = await $api_node
        ->setParentNode($node)
        ->setFieldsTree($this->fieldsTree)
        ->genResult();
      $nodes_data[] = $res;
    }
    return Map {
      'count' => $nodes_data->count(),
      'nodes' => $nodes_data,
    };
  }

  public function setFieldsTree(ImmMap<string, mixed> $fieldsTree): this {
    $this->fieldsTree = $fieldsTree;
    return $this;
  }
}
