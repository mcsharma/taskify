<?hh // strict


final class ApiTaskTagsEdge extends ApiEdge<Tag> {

  public function getEdgeClass(): classname<TaskToTagsEdge> {
    return TaskToTagsEdge::class;
  }

  public function getTargetNodeClass(): classname<ApiTagNode> {
    return ApiTagNode::class;
  }
}
