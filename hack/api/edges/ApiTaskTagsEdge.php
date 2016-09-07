<?hh // strict

require_once('api/fields/ApiEdge.php');
require_once('models/edges/TaskToTagsEdge.php');
require_once('api/nodes/ApiTagNode.php');

final class ApiTaskTagsEdge extends ApiEdge<Tag> {

  public function getEdgeClass(): classname<TaskToTagsEdge> {
    return TaskToTagsEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiTagNode> {
    return ApiTagNode::class;
  }
}
