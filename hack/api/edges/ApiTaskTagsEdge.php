<?hh // strict

require_once('hack/api/fields/ApiEdge.php');
require_once('hack/models/edges/TaskToTagsEdge.php');
require_once('hack/api/nodes/ApiTagNode.php');

final class ApiTaskTagsEdge extends ApiEdge<Tag> {

  public function getEdgeClass(): classname<TaskToTagsEdge> {
    return TaskToTagsEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiTagNode> {
    return ApiTagNode::class;
  }
}
