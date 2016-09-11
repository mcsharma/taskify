<?hh // strict

require_once('nodes/ApiUserNode.php');
require_once('nodes/ApiTaskNode.php');
require_once('IDUtil.php');
require_once('ApiList.php');

final class ApiServer {

  public static async function genResponseJson(
    string $path,
    ImmMap<string, string> $params,
  ): Awaitable<string> {
    $res_map = await self::genResponse($path, $params);
    if ($params->contains('pretty')) {
      return json_encode($res_map, JSON_PRETTY_PRINT);
    }
    return json_encode($res_map);
  }

  public static async function genResponse(
    string $path,
    ImmMap<string, string> $params,
  ): Awaitable<Map<string, mixed>> {
    if ($params->containsKey('method') &&
        strtolower($params['method']) === 'post') {
      return await self::genProcessPostRequest($path, $params);
    }
    $node_id = (int)$path;
    $type = IDUtil::idToType($node_id);

    $fields = $params->contains('fields')
      ? self::parseFieldMap($params['fields'])
      : ImmMap {};

    $node = await NodeBase::genDynamic($node_id);
    $api_node_class = IDUtil::nodeClassToApiNodeClass(get_class($node));
    $api_node = new $api_node_class();
    await $api_node
      ->setFieldsTree($fields)
      ->genSetRootNodeID($node_id);

    $res = await $api_node->genResult();
    if ($res === null) {
      $res = Map {
        "error" => "Invalid object ID",
      };
    }
    return $res;
  }

  private static async function genProcessPostRequest(
    string $path,
    ImmMap<string, string> $params,
  ): Awaitable<Map<string, mixed>> {
    $path_tokens = explode('/', $path);
    $node_id = array_shift($path_tokens);
    $edge_name = count($path_tokens) > 0 ? array_shift($path_tokens) : '';
    $node_id = (int)$node_id;
    $node_typename = IDUtil::idToApiTypename($node_id);
    $class_name = strtolower('api'.$node_typename.$edge_name.'post');
    $class_name = str_replace('_', '', $class_name);
    $post_api_classes = ApiList::post();
    $matched_api_classname = null;
    foreach ($post_api_classes as $api_class) {
        if (strtolower((string)$api_class) === $class_name) {
          $matched_api_classname = $api_class;
        }
    }
    invariant($matched_api_classname !== null, 'No Api class matched the given path');

    $api_class_obj = new $matched_api_classname();
    $processed_params_wrapped_result = await \HH\Asio\mmkw(
      $api_class_obj->paramDefinitions(),
      async ($name, $definition) ==> {
        $definition->setName($name);
        return await $definition->genProcessParam(
          $params->containsKey($name) ? $params[$name] : null,
        );
      },
    );

    $failed_param_errors = $processed_params_wrapped_result
      ->filter($result ==> $result->isFailed())
      ->map($result ==> $result->getException()->getMessage());
    if (!$failed_param_errors->isEmpty()) {
      throw new Exception('Invalid or missing parameters: '.json_encode($failed_param_errors));
    }

    $processed_params = $processed_params_wrapped_result
      ->map($result ==> $result->getResult())
      ->filter($value ==> $value !== null); // TODO implement a allowNull() flag in param base class

    return await $api_class_obj->genExecute(
      $node_id,
      $processed_params,
    );
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
