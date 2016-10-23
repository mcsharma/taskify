<?hh


final abstract class ApiList {
  public static function post(): ImmVector<classname<ApiPostBase>> {
    return ImmVector {
      ApiUserCreatedTasksPost::class,
      ApiTaskTagsPost::class,
      ApiTaskSubscribersPost::class,
      ApiTaskPost::class,
    };
  }

  public static function rootEdges(): ImmVector<classname<ApiRootEdgeBase<NodeBase>>> {
    return ImmVector {
      ApiUsersEdge::class,
      ApiTagsEdge::class,
    };
  }
}
