<?hh // strict

require_once('TaskifyDB.php');

<<__ConsistentConstruct>>
abstract class NodeBase {
    private int $id;
    private string $createdTime; // TODO change timestamps to int
    private string $updatedTime;

    public function __construct(
        private int $viewerID,
        Map<string, string> $node,
    ) {
        $this->id = (int)$node['id'];
        $this->createdTime = $node['created_time'];
        $this->updatedTime = $node['updated_time'];
    }

    public function getViewerID(): int {
      return $this->viewerID;
    }

    public function getID(): int {
        return $this->id;
    }

    public function getIDString(): string {
        return (string)$this->id;
    }

    public function getCreatedTime(): string {
        return $this->createdTime;
    }

    public function getUpdatedTime(): string {
        return $this->updatedTime;
    }

   /**
    * Call this function to load the Hack object model underlying the given ID.
    * Like, Task::gen() will load Task object.
    */
    public static async function gen(int $viewer_id, int $id): Awaitable<this> {
      $node = await TaskifyDB::genNode($id);
      return static::load($viewer_id, $node);
    }

    /**
     * Override in derived class if you need some custom logic, for example,
     * You might want the loaded object to be of a particiular subclass of the
     * class that is calling the gen() method.
     */
    protected static function load(int $viewer_id, Map<string, string> $node): this {
      return new static($viewer_id, $node);
    }

    public static async function genDynamic(
      int $viewer_id,
      int $id,
    ): Awaitable<NodeBase> {
      $class = IDUtil::idToNodeLoaderClass($id);
      return await $class::gen($viewer_id, $id);
    }
}
