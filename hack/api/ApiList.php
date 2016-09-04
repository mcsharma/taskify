<?hh

require_once ('hack/api/post/ApiUserCreatedTasksPost.php');
require_once ('hack/api/post/ApiTaskTagsPost.php');
require_once ('hack/api/post/ApiTaskSubscribersPost.php');

final abstract class ApiList {
  public static function post(): ImmVector<classname<ApiPostBase>> {
    return ImmVector {
      ApiUserCreatedTasksPost::class,
      ApiTaskTagsPost::class,
      ApiTaskSubscribersPost::class,
    };
  }
}
