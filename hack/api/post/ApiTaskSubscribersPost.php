<?hh // strict

require_once('ApiPostBase.php');
require_once('hack/api/params/ApiParamBase.php');
require_once('hack/api/params/ApiNodeIDParam.php');

final class ApiTaskSubscribersPost extends ApiPostBase {

  public function paramDefinitions(): ImmMap<string, ApiParamBase> {
    return ImmMap {
      'subscriber_id' => (new ApiNodeIDParam())->required(),
    };
  }

  public async function genExecute(
    int $task_id,
    Map<string, mixed> $params,
  ): Awaitable<Map<string, mixed>> {
    $subscriber_id = (int)$params['subscriber_id'];
    await TaskifyDB::genCreateEdge(
      EdgeType::TASK_TO_SUBSCRIBER,
      $task_id,
      $subscriber_id,
    );
    return Map {
      'id' => $subscriber_id,
    };
  }
}
