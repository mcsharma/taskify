<?hh // strict

require_once('ApiParamBase.php');

final class ApiStringParam extends ApiParamBase {

  private bool $allowEmpty = false;
  private bool $disableURLDecoding = false;

  public function allowEmpty(): this {
    $this->allowEmpty = true;
    return $this;
  }

  public function disableURLDecoding(): this {
    $this->disableURLDecoding = true;
    return $this;
  }

  protected async function genProcess(mixed $value): Awaitable<?string> {
    invariant(is_string($value), 'must be an string');
    if (!$this->allowEmpty) {
      $value = $value === '' ? null : $value;
      if ($this->isRequired) {
        $this->throwUnless(
          $value !== null,
          'Parameter is required to be a non-empty string',
        );
      }
    }
    if ($value !== null && !$this->disableURLDecoding) {
      $value = urldecode($value);
    }
    return $value;
  }
}
