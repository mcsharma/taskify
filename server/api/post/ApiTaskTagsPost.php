<?hh // strict

require_once('ApiPostBase.php');
require_once('api/params/ApiNodeIDParam.php');
require_once('api/params/ApiParamBase.php');

final class ApiTaskTagsPost extends ApiPostBase {

  public function paramDefinitions(): ImmMap<string, ApiParamBase> {
    return ImmMap {
      'tag_id' => (new ApiNodeIDParam())->required(),
    };
  }

  public async function genExecute(
    int $task_id,
    Map<string, mixed> $params,
  ): Awaitable<Map<string, mixed>> {
    $tag_id = (int)$params['tag_id'];
    await TaskifyDB::genCreateEdge(
      EdgeType::TASK_TO_TAG,
      $task_id,
      $tag_id,
    );
    return Map {
      'id' => $tag_id,
    };
  }
}
