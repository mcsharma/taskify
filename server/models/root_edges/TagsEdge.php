<?hh // strict

require_once('RootEdgeBase.php');

final class TagsEdge extends RootEdgeBase<Tag> {

  public async function genNodes(): Awaitable<ConstVector<Tag>> {
    $nodes = await TaskifyDB::genNodesForType(NodeType::TAG);
    return $nodes->map($node ==> new Tag($this->getViewerID(), $node));
  }
}
