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
    for ($i = 0; $i < strlen($str); $i++) {
      $c = $str[$i];
      if ($c === ',') {
        $ret[$field] = true;
        $field = '';
      } else if ($c === '{') {
        $child_query = substr($str, $i+1, $braces_map[$i] - $i - 1);
        $ret[$field] = self::parseFieldMap($child_query);
        $i = $braces_map[$i] + 1;
        $field = '';
      } else if ($c === '}') {
        invariant_violation('Should never come here');
      } else {
        $field.=$c;
      }
    }
    if ($field) {
      $ret[$field] = true;
    }
    return $ret->toImmMap();
  }
}
