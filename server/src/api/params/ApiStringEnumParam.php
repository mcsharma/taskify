<?hh // strict

require_once('ApiParamBase.php');

final class ApiStringEnumParam extends ApiParamBase {

  public function __construct(private array<string, string> $values) {
  }

  protected async function genProcess(mixed $value): Awaitable<string> {
    invariant(is_string($value), 'Invalid enum value');
    $this->throwUnless(in_array($value, $this->values), 'Invalid enum value');
    return $value;
  }
}
