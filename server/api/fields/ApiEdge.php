<?hh // strict

require_once('ApiFieldBase.php');

<<__ConsistentConstruct>>
abstract class ApiEdge<T as NodeBase> extends ApiFieldBase {

  private ?int $sourceID;
  private ImmMap<string, mixed> $fieldsTree = ImmMap {};
  private ImmMap<string, mixed> $params = ImmMap {};

  abstract public function getEdgeClass(): classname<EdgeBase<T>>;

  abstract public function getTargetNodeClass(): classname<ApiNode<T>>;

  public async function genResult(): Awaitable<?Map<string, mixed>> {
    $source_id = $this->sourceID;
    invariant($source_id !== null, 'source ID must have been set');
    $edge_class = $this->getEdgeClass();
    // TODO apply limit from param here
    $nodes = await (new $edge_class(
      $this->getViewerID(),
      $source_id,
    ))->genNodes();
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

  public function setSourceID(int $sourceID): this {
    $this->sourceID = $sourceID;
    return $this;
  }

  public function setFieldsTree(ImmMap<string, mixed> $fieldsTree): this {
    $this->fieldsTree = $fieldsTree;
    return $this;
  }

  public function setParams(ImmMap<string, string> $params): this {
    $this->params = $params;
    return $this;
  }
}
