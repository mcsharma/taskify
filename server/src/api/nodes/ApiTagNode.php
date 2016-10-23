<?hh // strict


final class ApiTagNode extends ApiNode<Tag> {

  public function getNodeClass(): classname<Tag> {
    return Tag::class;
  }

  public async function genFields(
  ): Awaitable<ImmMap<string, ApiFieldBase>> {
    return ImmMap {
      // TODO takeout common field defs into a trait
      'id' => ApiField::scalar('getID'),
      'created_time' => ApiField::scalar('getCreatedTime'),
      'updated_time' => ApiField::scalar('getUpdatedTime'),
      'caption' => ApiField::scalar('getCaption'),
      'description' => ApiField::scalar('getDescription'),
      'creator' => ApiField::node('getCreatorID', ApiUserNode::class),
    };
  }

  public function getDefaultFields(): Traversable<string> {
    return ImmVector {
      'id',
      'caption',
    };
  }
}
