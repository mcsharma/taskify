<?hh // strict

require_once('hack/api/fields/ApiNode.php');
require_once('hack/api/fields/ApiFieldBase.php');
require_once('hack/api/fields/ApiField.php');
require_once('hack/models/Activity.php');

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
    };
  }

  public function getDefaultFields(): Traversable<string> {
    return ImmVector {
      'id',
      'changed',
    };
  }
}
