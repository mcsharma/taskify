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
      'id' => ApiField::string('getIDString'),
      'created_time' => ApiField::string('getCreatedTime'),
      'updated_time' => ApiField::string('getUpdatedTime'),
      'actor' => ApiField::node('getActorID', ApiUserNode::class),
      'task' => ApiField::node('getTaskID', ApiTaskNode::class),
      'changed' => ApiField::string('getChangedField'),
      'old_title' => ApiField::string('getOldTitle'),
      'new_title' => ApiField::string('getNewTitle'),
      'old_description' => ApiField::string('getOldDescription'),
      'new_description' => ApiField::string('getNewDescription'),
      'new_status' => ApiField::string('getNewStatus'),
      'old_priority' => ApiField::string('getOldPriority'),
      'new_priority' => ApiField::string('getNewPriority'),
    };
  }

  public function getDefaultFields(): Traversable<string> {
    return ImmVector {
      'id',
      'changed',
    };
  }
}
