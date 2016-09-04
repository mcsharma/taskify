<?hh // strict

require_once('ApiNonEdgeField.php');
require_once('ApiEdge.php');

abstract class ApiNode<+T as NodeBase> extends ApiNonEdgeField {

  private ImmMap<string, mixed> $fieldsTree = ImmMap {};

  abstract public function getNodeClass(): classname<T>;

  abstract public function genFields(
  ): Awaitable<ImmMap<string, ApiFieldBase>>;

  abstract public function getDefaultFields(): Traversable<string>;

  public async function genSetRootNodeID(int $id): Awaitable<void> {
    $node_class = $this->getNodeClass();
    $node = await $node_class::gen($id);
    $this->setParentNode($node);
  }

  final public async function genResult(): Awaitable<Map<string, mixed>> {
    $res = await parent::genResult();
    $node = $res;
    if (is_int($res)) {
      $node_class = $this->getNodeClass();
      $node = await $node_class::gen($res);
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
      $field_obj = $field_defs[$field];
      $field_obj->setName($field)->setParentNode($node);
      if ($field_obj instanceof ApiNode || $field_obj instanceof ApiEdge) {
        if ($fields_tree === true) {
          $fields_tree = ImmMap {};
        }
        invariant($fields_tree instanceof ImmMap, 'Must be a Map');
        // TODO use a commmon interface
        // UNSAFE
        $field_obj->setFieldsTree(
          $fields_tree->contains('fields')
            ? $fields_tree['fields']
            : ImmMap {}
        );
        if ($field_obj instanceof ApiEdge) {
          $field_obj->setParams(
            $fields_tree->contains('params')
              ? $fields_tree['params']
              : ImmMap {}
          );
        }
      }
      // TODO avoid await in a loop
      $result = await $field_obj->genResult();
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
