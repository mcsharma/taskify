<?hh

require_once ('api/post/ApiUserCreatedTasksPost.php');
require_once ('api/post/ApiTaskTagsPost.php');
require_once ('api/post/ApiTaskPost.php');
require_once ('api/post/ApiTaskSubscribersPost.php');

final abstract class ApiList {
  public static function post(): ImmVector<classname<ApiPostBase>> {
    return ImmVector {
      ApiUserCreatedTasksPost::class,
      ApiTaskTagsPost::class,
      ApiTaskSubscribersPost::class,
      ApiTaskPost::class,
    };
  }
}
