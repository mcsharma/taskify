<?hh // strict

<<__ConsistentConstruct>>
abstract class ApiPostBase {

  abstract public function paramDefinitions(): ImmMap<string, ApiParamBase>;

  abstract public function genExecute(
    int $node_id,
    Map<string, mixed> $params,
  ): Awaitable<Map<string, mixed>>;
}
