<?hh // strict

require_once('ApiPostBase.php');
require_once('api/params/ApiStringParam.php');
require_once('api/params/ApiNodeIDParam.php');

final class ApiUserCreatedTasksPost extends ApiPostBase {

  public function paramDefinitions(): ImmMap<string, ApiParamBase> {
    return ImmMap {
      'title' => (new ApiStringParam())->required(),
      'description' => new ApiStringParam(),
      'owner_id' => new ApiNodeIDParam(),
    };
  }

  public async function genExecute(
    int $user_id,
    Map<string, mixed> $params,
  ): Awaitable<Map<string, mixed>> {
    $params['creator_id'] = $user_id;
    $task_id = await TaskifyDB::genCreateNode(NodeType::TASK, $params);
    await TaskifyDB::genCreateEdge(
      EdgeType::USER_TO_CREATED_TASK,
      $user_id,
      $task_id,
    );
    if ($params->contains('owner_id')) {
      // Add an edge from owner to task and vice-versa.
      await TaskifyDB::genCreateEdge(
        EdgeType::USER_TO_OWNED_TASK,
        (int)$params['owner_id'],
        $task_id,
      );
    }
    return Map {
      'id' => $task_id,
    };
  }
}
