<?hh // strict

require_once('hack/api/fields/ApiNode.php');
require_once('hack/api/fields/ApiFieldBase.php');
require_once('hack/api/fields/ApiField.php');
require_once('hack/models/Tag.php');

final class ApiTagNode extends ApiNode<Tag> {

  public function getNodeClass(): classname<Tag> {
    return Tag::class;
  }

  public async function genFields(
  ): Awaitable<ImmMap<string, ApiFieldBase>> {
    return ImmMap {
      // TODO takeout common field defs into a trait
      'id' => ApiField::string('getIDString'),
      'created_time' => ApiField::string('getCreatedTime'),
      'updated_time' => ApiField::string('getUpdatedTime'),
      'caption' => ApiField::string('getCaption'),
      'description' => ApiField::string('getDescription'),
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
