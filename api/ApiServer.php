<?hh // strict

require_once('api/nodes/ApiUserNode.php');
require_once('api/nodes/ApiTaskNode.php');

final class ApiServer {

  public static async function genResponseJson(
    string $path,
    Map<string, string> $params,
  ): Awaitable<string> {
    $res_map = await self::genResponse($path, $params);
    if ($params->contains('pretty')) {
      return json_encode($res_map, JSON_PRETTY_PRINT);
    }
    return json_encode($res_map);
  }

  public static async function genResponse(
    string $path,
    Map<string, string> $params,
  ): Awaitable<Map<string, mixed>> {

    $node_id = (int)$path;
    $type = IDUtil::idToType($node_id);

    $fields = $params->contains('fields')
      ? self::parseFieldMap($params['fields'])
      : ImmMap {};

    $api_node_class = null;
    switch ($type) {
      case NodeType::USER:
        $api_node_class = ApiUserNode::class;
        break;
      case NodeType::TASK:
        $api_node_class = ApiTaskNode::class;
        break;
    }

    $api_node = new $api_node_class();
    await $api_node
      ->setFieldsTree($fields)
      ->genSetRootNodeID($node_id);

    return await $api_node->genResult();
  }

  private static function parseFieldMap(string $str): ImmMap<string, mixed> {
    $stack = Vector {};
    $braces_map = Map {};
    for ($i = 0; $i < strlen($str); $i++) {
      if ($str[$i] === '{') {
        $stack[] = $i;
      } else if ($str[$i] === '}') {
        invariant ($stack->count() > 0, 'Invalid query string!');
        $last_open_pos = $stack->pop();
        $braces_map[$last_open_pos] = $i;
      }
    }

    $field = '';
    $ret = Map {};
    $child_fields = null;
    $str .= ',';
    for ($i = 0; $i < strlen($str); $i++) {
      $c = $str[$i];
      if ($c === ',') {
        list($field_name, $params) = self::extractParams($field);
        if ($params === null && $child_fields === null) {
          $ret[$field_name] = true;
        } else {
          $map = Map {};
          if ($params !== null) {
            $map['params'] = $params;
          }
          if ($child_fields !== null) {
            $map['fields'] = $child_fields;
          }
          $ret[$field_name] = $map->toImmMap();
        }
        $field = '';
        $child_fields = null;
      } else if ($c === '{') {
        $child_query = substr($str, $i+1, $braces_map[$i] - $i - 1);
        $child_fields = self::parseFieldMap($child_query);
        $i = $braces_map[$i];
      } else if ($c === '}') {
        invariant_violation('Should never come here');
      } else {
        $field.=$c;
      }
    }
    return $ret->toImmMap();
  }

  // Convert string like tasks.offset(10).limit(5)
  // into tuple('tasks', Map {offset => 10, limit => 5})
  private static function extractParams(
    string $str,
  ): (string, ?ImmMap<string, string>) {
    $list = explode('.', $str);
    $name = array_shift($list);
    if (count($list) === 0) {
      return tuple($name, null);
    }
    $params_map = Map {};
    foreach ($list as $param) {
      $open_pos = strpos($param, '(');
      $param_name = substr($param, 0, $open_pos);
      $param_val = substr($param, $open_pos + 1, strlen($param) - $open_pos - 2);
      $params_map[$param_name] = $param_val;
    }
    return tuple($name, $params_map->toImmMap());
  }
}
