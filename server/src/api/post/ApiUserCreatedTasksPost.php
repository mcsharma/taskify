<?hh // strict


final class ApiUserCreatedTasksPost extends ApiPostBase {

  public function paramDefinitions(): ImmMap<string, ApiParamBase> {
    return ImmMap {
      'title' => (new ApiStringParam())->required(),
      'status' => (new ApiStringEnumParam(TaskStatus::getValues()))
        ->defaultValue(TaskStatus::OPEN),
      'description' => new ApiStringParam(),
      'owner_id' => (new ApiNodeIDParam())->allowZero(),
      'priority' => new ApiStringEnumParam(Priority::getValues()),
      'tags' => new ApiVectorParam(new ApiNodeIDParam()),
      'subscribers' => new ApiVectorParam(new ApiNodeIDParam()),
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
    if ($params->contains('tags')) {
      $tags = $params['tags'];
      invariant($tags instanceof Vector, 'for hack');
      foreach ($tags as $tag_id) {
        await TaskifyDB::genCreateEdge(EdgeType::TASK_TO_TAG, $task_id, $tag_id);
      }
    }
    if ($params->contains('subscribers')) {
      $subscribers = $params['subscribers'];
      invariant($subscribers instanceof Vector, 'for hack');
      foreach ($subscribers as $subscriber_id) {
        await TaskifyDB::genCreateEdge(EdgeType::TASK_TO_SUBSCRIBER, $task_id, $subscriber_id);
      }
    }
    return Map {
      'id' => $task_id,
    };
  }
}
