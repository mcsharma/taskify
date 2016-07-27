<?hh // strict

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

    public static async function gen(int $id): Awaitable<this> {
      $node = await TaskifyDB::genNode($id);
      return new static($node);
    }

    public static async function genDynamic(int $id): Awaitable<NodeBase> {
      $class = IDUtil::idToNodeClass($id);
      return await $class::gen($id);
    }
}
