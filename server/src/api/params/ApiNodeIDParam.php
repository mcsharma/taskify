<?hh // strict

require_once('ApiParamBase.php');

// TODO accept node type in constructor
final class ApiNodeIDParam extends ApiParamBase {

  private bool $allowZero = false;

  protected async function genProcess(mixed $value): Awaitable<int> {
    $id = $value;
    if (is_string($id)) {
      $this->throwUnless(ctype_digit($id), 'Must be an integer');
      $id = (int)$id;
    }
    invariant(is_int($id), 'Must be an integer');
    $this->throwUnless(
      IDUtil::isValidID($id) || ($this->allowZero && $id === 0),
      'Invalid object ID',
    );
    return $id;
  }

  public function allowZero(): this {
    $this->allowZero = true;
    return $this;
  }
}
