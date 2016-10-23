<?hh // strict

require_once('api/fields/ApiNode.php');
require_once('api/fields/ApiFieldBase.php');
require_once('api/fields/ApiField.php');
require_once('models/Activity.php');

final class ApiActivityNode extends ApiNode<Activity> {

  public function getNodeClass(): classname<Activity> {
    return Activity::class;
  }

  public async function genFields(
  ): Awaitable<ImmMap<string, ApiFieldBase>> {
    return ImmMap {
      'id' => ApiField::scalar('getID'),
      'created_time' => ApiField::scalar('getCreatedTime'),
      'updated_time' => ApiField::scalar('getUpdatedTime'),

      'actor' => ApiField::node('getActorID', ApiUserNode::class),
      'task' => ApiField::node('getTaskID', ApiTaskNode::class),
      'changed' => ApiField::scalar('getChangedField'),

      'old_title' => ApiField::scalar('getOldTitle'),
      'new_title' => ApiField::scalar('getNewTitle'),

      'old_description' => ApiField::scalar('getOldDescription'),
      'new_description' => ApiField::scalar('getNewDescription'),

      'new_status' => ApiField::scalar('getNewStatus'),

      'old_priority' => ApiField::scalar('getOldPriority'),
      'new_priority' => ApiField::scalar('getNewPriority'),

      'added_tags' => ApiField::listOfNodes('getAddedTagIDs', ApiTagNode::class),
      'removed_tags' => ApiField::listOfNodes('getRemovedTagIDs', ApiTagNode::class),

      'added_subscribers' => ApiField::listOfNodes('getAddedSubscriberIDs', ApiUserNode::class),
      'removed_subscribers' => ApiField::listOfNodes('getRemovedSubscriberIDs', ApiUserNode::class),

      'old_owner' => ApiField::node('getOldOwnerID', ApiUserNode::class),
      'new_owner' => ApiField::node('getNewOwnerID', ApiUserNode::class),
    };
  }

  public function getDefaultFields(): Traversable<string> {
    return ImmVector {
      'id',
      'changed',
    };
  }
}
