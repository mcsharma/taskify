<?hh // strict

require_once ('api/params/ApiStringParam.php');

final class ApiTaskPost extends ApiPostBase {

  public function paramDefinitions(): ImmMap<string, ApiParamBase> {
    return ImmMap {
      'title' => new ApiStringParam(),
      'description' => (new ApiStringParam())->allowEmpty(),
    };
  }

  public async function genExecute(
    int $node_id,
    Map<string, mixed> $params,
  ): Awaitable<Map<string, mixed>> {
    // Encapsulare this logic into a seprate TaskUpdater
    $task = await Task::gen($node_id);
    await TaskifyDB::genUpdateNode($node_id, $params);
    $updated_task = await Task::gen($node_id);
    if ($updated_task->getTitle() !== $task->getTitle()) {
      $actor_id = 1;
      $activity_id = await TaskifyDB::genCreateNode(NodeType::ACTIVITY, Map {
        'task_id' => $node_id,
        'actor_id' => $actor_id,
        'changed' => TaskField::TITLE,
        'old_title' => $task->getTitle(),
        'new_title' => $updated_task->getTitle(),
      });
      await TaskifyDB::genCreateEdge(EdgeType::ACTIVITY_TO_ACTOR, $activity_id, $actor_id);
      await TaskifyDB::genCreateEdge(EdgeType::ACTIVITY_TO_TASK, $activity_id, $node_id);
    }

    return Map {
      'success' => true,
    };
  }
}
