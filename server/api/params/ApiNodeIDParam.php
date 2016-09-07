<?hh // strict

require_once('ApiParamBase.php');

final class ApiNodeIDParam extends ApiParamBase {

  protected async function genProcess(string $value): Awaitable<int> {
    $this->throwUnless(ctype_digit($value), 'Must be an integer');
    $id = (int)$value;
    $this->throwUnless(IDUtil::isValidID($id), 'Invalid object ID');
    return $id;
  }
}
