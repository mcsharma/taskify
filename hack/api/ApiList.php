<?hh

require_once ('hack/api/post/ApiUserCreatedTasksPost.php');

final abstract class ApiList {
  public static function post(): ImmVector<classname<ApiPostBase>> {
    return ImmVector {
      ApiUserCreatedTasksPost::class,
    };
  }
}
