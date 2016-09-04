<?hh // strict

require_once('ApiParamBase.php');

final class ApiStringEnumParam extends ApiParamBase {

  public function __construct(private array<string, string> $values) {
  }

  protected async function genProcess(string $value): Awaitable<string> {
    $this->throwUnless(in_array($value, $this->values), 'Invalid enum value');
    return $value;
  }
}
