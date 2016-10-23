<?hh // strict

<<__ConsistentConstruct>>
abstract class ApiPostBase {

  public function __construct(private int $viewerID) {
  }

  abstract public function paramDefinitions(): ImmMap<string, ApiParamBase>;

  abstract public function genExecute(
    int $node_id,
    Map<string, mixed> $params,
  ): Awaitable<Map<string, mixed>>;

  public function getViewerID(): int {
    return $this->viewerID;
  }
}
