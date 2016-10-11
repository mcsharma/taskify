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

  final public async function genNodes(): Awaitable<Vector<T>> {
    $edges = await $this->genIDs();
    $node_type = $this->getTargetNodeType();
    $nodes = Vector {};
    foreach ($edges as $edge) {
      $id2 = (int)$edge['id2'];
      // TODO avoid await in a loop
      $node = await $node_type::gen($this->viewerID, $id2);
      $nodes[] = $node;
    }

    return $nodes;
  }

  final public async function genIDs(): Awaitable<Vector<Map<string, mixed>>> {
    $res = await TaskifyDB::genEdgesForType(
      $this->sourceID,
      $this->getEdgeType(),
    );
    return $res->map($data ==> {
      $mixed_data = new Map($data);
      $mixed_data['id2'] = (int)$mixed_data['id2'];
      // TODO typecast timestamps to ints too once they are mvoed to ints
      // and then change the typehint to Map<string, int>
      return $mixed_data;
    });

  }
}
