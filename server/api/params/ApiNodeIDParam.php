<?hh // strict

require_once('ApiParamBase.php');

final class ApiNodeIDParam extends ApiParamBase {

  private bool $allowZero = false;

  protected async function genProcess(string $value): Awaitable<int> {
    $this->throwUnless(ctype_digit($value), 'Must be an integer');
    $id = (int)$value;
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
