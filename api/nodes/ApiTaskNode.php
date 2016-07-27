<?hh // strict

require_once('api/fields/ApiNode.php');
require_once('api/fields/ApiFieldBase.php');
require_once('api/fields/ApiField.php');

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
    };
  }

  public function getDefaultFields(): Traversable<string> {
    return ImmVector {
      'id',
      'title',
    };
  }
}
