<?hh

require_once('ApiFieldBase.php');

class ApiScalarField extends ApiFieldBase {

  // Instance method that it's parent will use on its rawFieldValue to get the
  // rawFieldValue of this field. Note that when this field is root
  // $method will not be set. Like for  /<user_id>?fields={...} user node field
  // is a root field.
  private ?string $method;
  // Raw value of this field, like for string field this will be string
  // itself, for node field it will be the node which this api composite field
  // exposes data of.
  private mixed $rawFieldValue;

  public function setRawFieldValue(mixed $node): this {
    $this->rawFieldValue = $node;
    return $this;
  }

  public function getRawFieldValue(): mixed {
    return $this->rawFieldValue;
  }

  public function setMethod(string $method): this {
    $this->method = $method;
    return $this;
  }

  public function getMethod(): ?string {
    return $this->method;
  }

  public async function genResult(): Awaitable<mixed> {
    // For scaler field values, we don't need to transform them to anything
    // so we can just return them as it is. Derived class can apporpriately
    // override this function to transoform result.
    return $this->getRawFieldValue();
  }
}
