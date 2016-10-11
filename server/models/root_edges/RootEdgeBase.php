<?hh // strict

// TODO add limit, maxTime minTime filters. And use a trait to do that, so that
// they can be used in non-root edge class too.
<<__ConsistentConstruct>>
abstract class RootEdgeBase<+T as NodeBase> {

  public function __construct(private int $viewerID) {
  }

  public function getViewerID(): int {
    return $this->viewerID;
  }

  abstract public function genNodes(): Awaitable<ConstVector<T>>;
}
