<?hh // strict

require_once('ApiFieldBase.php');
require_once('api/common/TApiEdgeCommon.php');

<<__ConsistentConstruct>>
abstract class ApiEdge<T as NodeBase> extends ApiFieldBase {

  use TApiEdgeCommon<T>;

  private ?int $sourceID;

  public function setSourceID(int $sourceID): this {
    $this->sourceID = $sourceID;
    return $this;
  }

  abstract public function getEdgeClass(): classname<EdgeBase<T>>;

  public async function genResult(): Awaitable<Map<string, mixed>> {
    $source_id = $this->sourceID;
    invariant($source_id !== null, 'source ID must have been set');
    $edge_class = $this->getEdgeClass();
    // TODO apply limit from param here
    $nodes = await (new $edge_class(
      $this->getViewerID(),
      $source_id,
    ))->genNodes();
    return await $this->genNodesToApiResult($nodes);
  }

}
