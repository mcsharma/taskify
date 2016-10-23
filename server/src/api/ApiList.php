<?hh

require_once ('api/post/ApiUserCreatedTasksPost.php');
require_once ('api/post/ApiTaskTagsPost.php');
require_once ('api/post/ApiTaskPost.php');
require_once ('api/post/ApiTaskSubscribersPost.php');
require_once ('api/root_edges/ApiUsersEdge.php');
require_once ('api/root_edges/ApiRootEdgeBase.php');
require_once ('api/root_edges/ApiTagsEdge.php');

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
