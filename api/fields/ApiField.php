<?hh // strict

require_once('api/fields/ApiNonEdgeField.php');
require_once('api/edges/ApiUserTasksEdge.php');
require_once('api/fields/ApiNode.php');

abstract final class ApiField {

  public static function string(string $method_name): ApiNonEdgeField {
    return (new ApiNonEdgeField())->setMethod($method_name);
  }

  public static function node<T as NodeBase>(
    string $method_name,
    classname<ApiNode<T>> $node_class,
  ): ApiNode<T> {
    return (new $node_class())->setMethod($method_name);
  }

  public static function edge<T as NodeBase>(
    classname<ApiEdge<T>> $edge_class,
  ): ApiEdge<T> {
    return new $edge_class();
  }
}
