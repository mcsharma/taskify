<?hh // strict

require_once('ApiParamBase.php');

final class ApiVectorParam extends ApiParamBase {

  public function __construct(private ApiParamBase $elementType) {
  }

  protected async function genProcess(mixed $value): Awaitable<Vector<mixed>> {
    $arr = json_decode($value, true);
    invariant(is_array($arr) && $arr === array_values($arr), 'Invalid param, must be a valid json encoded array');
    $res = Vector {};
    foreach ($arr as $raw_item) {
      try {
        // TODO avoid await in a loop
        $item = await $this->elementType->genProcessParam($raw_item);
      } catch (Exception $e) {
        throw new Exception('Invalid value(s) present in array: '.$e->getMessage());
      }
      $res[] = $item;
    }
    echo $res;
    return $res;
  }
}
