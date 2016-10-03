<?hh // strict

<<__ConsistentConstruct>>
abstract class EdgeBase<T as NodeBase> {

  public function __construct(private int $viewerID, private int $sourceID) {
  }

  abstract public function getEdgeType(): EdgeType;

  abstract public function getTargetNodeType(): classname<T>;

  public function getViewerID(): int {
    return $this->viewerID;
  }

  final public async function genNodes(): Awaitable<Map<int, T>> {
    $edges = await TaskifyDB::genEdgesForType(
      $this->sourceID,
      $this->getEdgeType()
    );
    $node_type = $this->getTargetNodeType();
    $nodes = Map {};
    foreach ($edges as $edge) {
      $id2 = (int)$edge['id2'];
      // TODO avoid await in a loop
      $node = await $node_type::gen($this->viewerID, $id2);
      $nodes[$id2] = $node;
    }

    return $nodes;
  }
}
