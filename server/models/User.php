<?hh // strict

require_once('NodeBase.php');

final class User extends NodeBase {

    private string $firstName;
    private string $lastName;
    private string $email;

    public function __construct(
        Map<string, string> $node,
    ) {
        parent::__construct($node);
        $data = json_decode($node['data']);

        $this->firstName = $data->first_name;
        $this->lastName = $data->last_name;
        $this->email = $data->email;
    }

    public function getFirstName(): string {
        return $this->firstName;
    }

    public function getLastName(): string {
        return $this->lastName;
    }

    public function getName(): string {
      return $this->firstName.' '.$this->lastName;
    }

    public function getEmail(): string {
        return $this->email;
    }
}
