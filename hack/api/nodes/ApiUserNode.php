<?hh // strict

require_once('hack/api/fields/ApiNode.php');
require_once('hack/api/fields/ApiFieldBase.php');
require_once('hack/api/edges/ApiUserTasksEdge.php');
require_once('hack/api/edges/ApiUserCreatedTasksEdge.php');
require_once('hack/models/User.php');

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
      'created_tasks' => ApiField::edge(ApiUserCreatedTasksEdge::class),
    };
  }

  public function getDefaultFields(): Traversable<string> {
    return ImmVector {
      'id',
      'name',
    };
  }
}
