<?hh // strict

require_once ('api/root_edges/ApiRootEdgeBase.php');
require_once ('models/root_edges/TagsEdge.php');

final class ApiTagsEdge extends ApiRootEdgeBase<Tag> {

  public function getRootEdgeClass(): classname<TagsEdge> {
    return TagsEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiTagNode> {
    return ApiTagNode::class;
  }
}
