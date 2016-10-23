<?hh // strict

require_once('api/fields/ApiNode.php');
require_once('api/fields/ApiFieldBase.php');
require_once('api/edges/ApiUserTasksEdge.php');
require_once('api/edges/ApiUserCreatedTasksEdge.php');
require_once('api/edges/ApiUserActivitiesEdge.php');
require_once('models/User.php');

final class ApiUserNode extends ApiNode<User> {

  public function getNodeClass(): classname<User> {
    return User::class;
  }

  public async function genFields(
  ): Awaitable<ImmMap<string, ApiFieldBase>> {
    return ImmMap {
      'id' => ApiField::scalar('getID'),
      'created_time' => ApiField::scalar('getCreatedTime'),
      'email' => ApiField::scalar('getEmail'),
      'name' => ApiField::scalar('getName'),
      'updated_time' => ApiField::scalar('getUpdatedTime'),
      'tasks' => ApiField::edge(ApiUserTasksEdge::class),
      'created_tasks' => ApiField::edge(ApiUserCreatedTasksEdge::class),
      'activities' => ApiField::edge(ApiUserActivitiesEdge::class),
    };
  }

  public function getDefaultFields(): Traversable<string> {
    return ImmVector {
      'id',
      'name',
    };
  }
}
