<?hh // strict

require_once('api/fields/ApiNode.php');
require_once('api/fields/ApiFieldBase.php');

final class ApiUserNode extends ApiNode<User> {

  public function getNodeClass(): classname<User> {
    return User::class;
  }

  public async function genFields(
  ): Awaitable<ImmMap<string, ApiFieldBase>> {
    return ImmMap {
      'id' => ApiField::string('getIDString'),
      'created_time' => ApiField::string('getCreatedTime'),
      'email' => ApiField::string('getEmail'),
      'name' => ApiField::string('getName'),
      'updated_time' => ApiField::string('getUpdatedTime'),
      'tasks' => ApiField::edge(ApiUserTasksEdge::class),
    };
  }

  public function getDefaultFields(): Traversable<string> {
    return ImmVector {
      'id',
      'name',
    };
  }
}
