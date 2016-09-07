<?hh // strict

require_once('taskifyDB.php');

<<__ConsistentConstruct>>
abstract class NodeBase {
    private int $id;
    private string $createdTime; // TODO change timestamps to int
    private string $updatedTime;

    public function __construct(
        Map<string, string> $node,
    ) {
        $this->id = (int)$node['id'];
        $this->createdTime = $node['created_time'];
        $this->updatedTime = $node['updated_time'];
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
    public static async function gen(int $id): Awaitable<this> {
      $node = await TaskifyDB::genNode($id);
      return static::load($node);
    }

    /**
     * Override in derived class if you need some custom logic, for example,
     * You might want the loaded object to be of a particiular subclass of the
     * class that is calling the gen() method.
     */
    protected static function load(Map<string, string> $node): this {
      return new static($node);
    }

    public static async function genDynamic(int $id): Awaitable<NodeBase> {
      $class = IDUtil::idToNodeLoaderClass($id);
      return await $class::gen($id);
    }
}
