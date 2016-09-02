<?hh // strict

abstract class ApiParamBase {

  protected ?string $name;
  protected bool $isRequired = false;
  protected mixed $defaultValue;

  public async function genProcessParam(?string $value): Awaitable<mixed> {
    if ($this->isRequired() && $value === null) {
      throw new Exception('Parameter is required');
    }
    if ($value !== null) {
      $value = await $this->genProcess($value);
    }
    if ($value === null) {
      $value = $this->defaultValue;
    }
    return $value;
  }

  abstract protected function genProcess(?string $value): Awaitable<mixed>;

  public function required(): this {
    $this->isRequired = true;
    return $this;
  }

  public function defaultValue(mixed $value): this {
    $this->defaultValue = $value;
    return $this;
  }

  public function isRequired(): bool {
    return $this->isRequired;
  }

  public function setName(string $name): this {
    $this->name = $name;
    return $this;
  }

  public function getName(): ?string {
    return $this->name;
  }

  protected function throwUnless(bool $condition, string $message): void {
    if (!$condition) {
      // TODO Make a specific exception class
      throw new Exception($message);
    }
  }
}
