<?hh // strict

require_once('RootEdgeBase.php');

final class UsersEdge extends RootEdgeBase<User> {

  public async function genNodes(): Awaitable<ConstVector<User>> {
    $nodes = await TaskifyDB::genNodesForType(NodeType::USER);
    return $nodes->map($node ==> new User($this->getViewerID(), $node));
  }
}
