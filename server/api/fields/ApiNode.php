<?hh

require_once('ApiScalarField.php');
require_once('ApiEdge.php');

<<__ConsistentConstruct>>
abstract class ApiNode<+T as NodeBase> extends ApiScalarField {

  private ImmMap<string, mixed> $fieldsTree = ImmMap {};

  abstract public function getNodeClass(): classname<T>;

  abstract public function genFields(
  ): Awaitable<ImmMap<string, ApiFieldBase>>;

  // Override if you want to return more than just id field by default.
  public function getDefaultFields(): Traversable<string> {
    return Vector {
      'id',
    };
  }

  final public async function genResult(): Awaitable<?Map<string, mixed>> {
    $raw_value = $this->getRawFieldValue();
    if ($raw_value === null) {
      return null;
    }
    $node = $raw_value;
    if (is_int($raw_value)) {
      $node_class = $this->getNodeClass();
      $node = await $node_class::gen($this->getViewerID(), $raw_value);
    }
    invariant($node instanceof NodeBase, 'Must be a nodebase');
    $field_defs = await $this->genFields();
    $response = Map {};
    $fields = $this->fieldsTree->toMap();
    if ($fields->count() === 0) {
      foreach ($this->getDefaultFields() as $field) {
        $fields[$field] = true;
      }
    }
    foreach ($fields as $field => $fields_tree) {
      if (!$field_defs->containsKey($field)) {
        throw new Exception(sprintf("Requested non-existing field '%s'", $field));
      }
      $sub_field_obj = $field_defs[$field]
       ->setName($field)
       ->setViewerID($this->getViewerID());
      if ($sub_field_obj instanceof ApiScalarField) {
        $method = $sub_field_obj->getMethod();
        $sub_field_value = $node->$method();
        if ($sub_field_value instanceof Awaitable) {
          $sub_field_value = await $sub_field_value;
        }
        $sub_field_obj->setRawFieldValue($sub_field_value);
      } else if ($sub_field_obj instanceof ApiEdge) {
        $sub_field_obj->setSourceID($node->getID());
      }


      if ($sub_field_obj instanceof ApiNode || $sub_field_obj instanceof ApiEdge) {
        if ($fields_tree === true) {
          $fields_tree = ImmMap {};
        }
        invariant($fields_tree instanceof ImmMap, 'Must be a Map');
        // TODO use a commmon interface
        // UNSAFE
        $sub_field_obj->setFieldsTree(
          $fields_tree->contains('fields')
            ? $fields_tree['fields']
            : ImmMap {}
        );
        if ($sub_field_obj instanceof ApiEdge) {
          $sub_field_obj->setParams(
            $fields_tree->contains('params')
              ? $fields_tree['params']
              : ImmMap {}
          );
        }
      }
      // TODO avoid await in a loop
      $result = await $sub_field_obj->genResult();
      if ($result !== null) {
        $response[$field] = $result;
      }
    }
    return $response;
  }

  public function setFieldsTree(ImmMap<string, mixed> $fieldsTree): this {
    $this->fieldsTree = $fieldsTree;
    return $this;
  }
}
