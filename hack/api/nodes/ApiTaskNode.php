<?hh // strict

require_once('hack/api/fields/ApiNode.php');
require_once('hack/api/fields/ApiFieldBase.php');
require_once('hack/api/fields/ApiField.php');
require_once('hack/models/Task.php');
require_once('hack/api/edges/ApiTaskTagsEdge.php');
require_once('hack/api/edges/ApiTaskSubscribersEdge.php');

final class ApiTaskNode extends ApiNode<Task> {

  public function getNodeClass(): classname<Task> {
    return Task::class;
  }

  public async function genFields(
  ): Awaitable<ImmMap<string, ApiFieldBase>> {
    return ImmMap {
      'id' => ApiField::string('getIDString'),
      'created_time' => ApiField::string('getCreatedTime'),
      'updated_time' => ApiField::string('getUpdatedTime'),
      'title' => ApiField::string('getTitle'),
      'description' => ApiField::string('getDescription'),
      'owner' => ApiField::node('getOwnerID', ApiUserNode::class),
      'priority' => ApiField::string('getPriority'),
      'tags' => ApiField::edge(ApiTaskTagsEdge::class),
      'subscribers' => ApiField::edge(ApiTaskSubscribersEdge::class),
    };
  }

  public function getDefaultFields(): Traversable<string> {
    return ImmVector {
      'id',
      'title',
    };
  }
}
