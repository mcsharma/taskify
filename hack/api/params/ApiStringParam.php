<?hh // strict

require_once('ApiParamBase.php');

final class ApiStringParam extends ApiParamBase {

  private bool $allowEmpty = false;

  public function allowEmpty(): this {
    $this->allowEmpty = true;
    return $this;
  }

  protected async function genProcess(string $value): Awaitable<?string> {
    if (!$this->allowEmpty) {
      $value = $value === '' ? null : $value;
      if ($this->isRequired) {
        $this->throwUnless(
          $value !== null,
          'Parameter is required to be a non-empty string',
        );
      }
    }
    return $value;
  }
}
