<?hh // strict


abstract final class ApiField {

  public static function scalar(string $method_name): ApiScalarField {
    return (new ApiScalarField())->setMethod($method_name);
  }

  public static function listOfNodes<T as NodeBase>(
    string $method_name,
    classname<ApiNode<T>> $node_class,
  ): ApiNodeListField<T> {
    return new ApiNodeListField($method_name, $node_class);
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
