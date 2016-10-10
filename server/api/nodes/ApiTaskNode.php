<?hh // strict

require_once('api/fields/ApiNode.php');
require_once('api/fields/ApiFieldBase.php');
require_once('api/fields/ApiField.php');
require_once('models/Task.php');
require_once('api/edges/ApiTaskTagsEdge.php');
require_once('api/edges/ApiTaskSubscribersEdge.php');
require_once('api/edges/ApiTaskActivitiesEdge.php');

final class ApiTaskNode extends ApiNode<Task> {

  public function getNodeClass(): classname<Task> {
    return Task::class;
  }

  public async function genFields(
  ): Awaitable<ImmMap<string, ApiFieldBase>> {
    return ImmMap {
      'id' => ApiField::scalar('getIDString'),
      'created_time' => ApiField::scalar('getCreatedTime'),
      'updated_time' => ApiField::scalar('getUpdatedTime'),
      'status' => ApiField::scalar('getStatus'),
      'title' => ApiField::scalar('getTitle'),
      'description' => ApiField::scalar('getDescription'),
      'creator' => ApiField::node('getCreatorID', ApiUserNode::class),
      'owner' => ApiField::node('getOwnerID', ApiUserNode::class),
      'priority' => ApiField::scalar('getPriority'),
      'tags' => ApiField::edge(ApiTaskTagsEdge::class),
      'subscribers' => ApiField::edge(ApiTaskSubscribersEdge::class),
      'activities' => ApiField::edge(ApiTaskActivitiesEdge::class),
    };
  }

  public function getDefaultFields(): Traversable<string> {
    return ImmVector {
      'id',
      'title',
    };
  }
}
