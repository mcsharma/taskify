<?hh

require_once ('api/post/ApiUserTasksPost.php');

final abstract class ApiList {
  public static function post(): ImmVector<classname<ApiPostBase>> {
    return ImmVector {
      ApiUserTasksPost::class,
    };
  }
}
