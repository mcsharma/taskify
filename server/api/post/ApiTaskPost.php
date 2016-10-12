<?hh // strict

require_once ('api/params/ApiStringParam.php');
require_once ('api/params/ApiVectorParam.php');

final class ApiTaskPost extends ApiPostBase {

  public function paramDefinitions(): ImmMap<string, ApiParamBase> {
    return ImmMap {
      'title' => new ApiStringParam(),
      'description' => (new ApiStringParam())->allowEmpty(),
      'status' => (new ApiStringEnumParam(TaskStatus::getValues())),
      'owner_id' => (new ApiNodeIDParam())->allowZero(),
      'priority' => new ApiStringEnumParam(Priority::getValues()),
      'tags' => new ApiVectorParam(new ApiNodeIDParam()),
      'subscribers' => new ApiVectorParam(new ApiNodeIDParam()),
    };
  }

  public async function genExecute(
    int $node_id,
    Map<string, mixed> $params,
  ): Awaitable<Map<string, mixed>> {
    $params = clone $params;
    invariant(
      $params->count() <= $this->paramDefinitions()->count(),
      "genExecute() shouldn't be sent any other param except the defined ones",
    );
    // Encapsulare this logic into a seprate TaskUpdater
    $task = await Task::gen($this->getViewerID(), $node_id);
    $actor_id = $this->getViewerID();
    if ($params->contains('tags')) {
      $old_tags = await $task->genTagIDs();
      $old_tags = $old_tags->toArray();
      $new_tags = $params['tags'];
      invariant($new_tags instanceof Vector, 'for hack');
      $new_tags = $new_tags->toArray();
      $added_tags = array_values(array_diff($new_tags, $old_tags));
      $removed_tags = array_values(array_diff($old_tags, $new_tags));

      if (count($added_tags) > 0 || count($removed_tags) > 0) {
        foreach ($added_tags as $added_tag) {
          await TaskifyDB::genCreateEdge(EdgeType::TASK_TO_TAG, $node_id, $added_tag);
        }
        foreach ($removed_tags as $removed_tag) {
          await TaskifyDB::genDeleteEdge(EdgeType::TASK_TO_TAG, $node_id, $removed_tag);
        }
        $activity_id = await TaskifyDB::genCreateNode(NodeType::ACTIVITY, Map {
          'task_id' => $node_id,
          'actor_id' => $actor_id,
          'changed' => TaskField::TAGS,
          'added' => $added_tags,
          'removed' => $removed_tags,
        });
        await TaskifyDB::genCreateEdge(EdgeType::ACTIVITY_TO_ACTOR, $activity_id, $actor_id);
        await TaskifyDB::genCreateEdge(EdgeType::ACTIVITY_TO_TASK, $activity_id, $node_id);
      }
      $params->remove('tags');
    }

    if ($params->contains('subscribers')) {
      $old_subscribers = await $task->genSubscriberIDs();
      $old_subscribers = $old_subscribers->toArray();
      $new_subscribers = $params['subscribers'];
      invariant($new_subscribers instanceof Vector, 'for hack');
      $new_subscribers = $new_subscribers->toArray();
      $added_subscribers = array_values(array_diff($new_subscribers, $old_subscribers));
      $removed_subscribers = array_values(array_diff($old_subscribers, $new_subscribers));

      if (count($added_subscribers) > 0 || count($removed_subscribers) > 0) {
        foreach ($added_subscribers as $added_subscriber) {
          await TaskifyDB::genCreateEdge(EdgeType::TASK_TO_SUBSCRIBER, $node_id, $added_subscriber);
        }
        foreach ($removed_subscribers as $removed_subscriber) {
          await TaskifyDB::genDeleteEdge(EdgeType::TASK_TO_SUBSCRIBER, $node_id, $removed_subscriber);
        }
        $activity_id = await TaskifyDB::genCreateNode(NodeType::ACTIVITY, Map {
          'task_id' => $node_id,
          'actor_id' => $actor_id,
          'changed' => TaskField::SUBSCRIBERS,
          'added' => $added_subscribers,
          'removed' => $removed_subscribers,
        });
        await TaskifyDB::genCreateEdge(EdgeType::ACTIVITY_TO_ACTOR, $activity_id, $actor_id);
        await TaskifyDB::genCreateEdge(EdgeType::ACTIVITY_TO_TASK, $activity_id, $node_id);
      }
      $params->remove('subscribers');
    }

    await TaskifyDB::genUpdateNode($node_id, $params);
    $updated_task = await Task::gen($this->getViewerID(), $node_id);
    if ($updated_task->getTitle() !== $task->getTitle()) {
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
    if ($updated_task->getDescription() !== $task->getDescription()) {
      $activity_id = await TaskifyDB::genCreateNode(NodeType::ACTIVITY, Map {
        'task_id' => $node_id,
        'actor_id' => $actor_id,
        'changed' => TaskField::DESCRIPTION,
        'old_description' => $task->getDescription(),
        'new_description' => $updated_task->getDescription(),
      });
      await TaskifyDB::genCreateEdge(EdgeType::ACTIVITY_TO_ACTOR, $activity_id, $actor_id);
      await TaskifyDB::genCreateEdge(EdgeType::ACTIVITY_TO_TASK, $activity_id, $node_id);
    }
    if ($updated_task->getStatus() !== $task->getStatus()) {
      $activity_id = await TaskifyDB::genCreateNode(NodeType::ACTIVITY, Map {
        'task_id' => $node_id,
        'actor_id' => $actor_id,
        'changed' => TaskField::STATUS,
        'new_status' => $updated_task->getStatus(),
      });
      await TaskifyDB::genCreateEdge(EdgeType::ACTIVITY_TO_ACTOR, $activity_id, $actor_id);
      await TaskifyDB::genCreateEdge(EdgeType::ACTIVITY_TO_TASK, $activity_id, $node_id);
    }
    if ($updated_task->getPriority() !== $task->getPriority()) {
      $activity_id = await TaskifyDB::genCreateNode(NodeType::ACTIVITY, Map {
        'task_id' => $node_id,
        'actor_id' => $actor_id,
        'changed' => TaskField::PRIORITY,
        'old_priority' => $task->getPriority(),
        'new_priority' => $updated_task->getPriority(),
      });
      await TaskifyDB::genCreateEdge(EdgeType::ACTIVITY_TO_ACTOR, $activity_id, $actor_id);
      await TaskifyDB::genCreateEdge(EdgeType::ACTIVITY_TO_TASK, $activity_id, $node_id);
    }

    return Map {
      'success' => true,
    };
  }
}
