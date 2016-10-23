<?hh // strict


final class User extends NodeBase {

    private string $firstName;
    private string $lastName;
    private ?string $email;

    public function __construct(
        int $viewerID,
        Map<string, string> $node,
    ) {
        parent::__construct($viewerID, $node);
        $data = json_decode($node['data'], true);

        $this->firstName = $data['first_name'];
        $this->lastName = $data['last_name'];
        $this->email = idx($data, 'email');
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

    public function getEmail(): ?string {
        return $this->email;
    }
}
