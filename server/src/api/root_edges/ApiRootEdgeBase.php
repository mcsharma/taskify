<?hh // strict

<<__ConsistentConstruct>>
abstract class ApiRootEdgeBase<+T as NodeBase> {

  use TApiEdgeCommon<T>;

  // TODO add a trait for viewer id.
  public function __construct(private int $viewerID) {
  }


  abstract public function getRootEdgeClass(): classname<RootEdgeBase<T>>;

  // TODO: Define a shape for edge responses
  public async function genResult(): Awaitable<Map<string, mixed>> {
    $edge_class = $this->getRootEdgeClass();
    $nodes = await (new $edge_class(
      $this->getViewerID(),
    ))->genNodes();
    return await $this->genNodesToApiResult($nodes);
  }

  public function getViewerID(): int {
    return $this->viewerID;
  }
}
