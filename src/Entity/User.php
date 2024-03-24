<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Stringable;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class User implements UserInterface, Stringable
{
    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'uuid')]
    private ?UuidInterface $idpId = null;

    #[ORM\Column(type: 'string', unique: true)]
    #[Assert\NotBlank]
    private string $username;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $firstname;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $lastname;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [ 'ROLE_USER' ];

    /**
     * @var string[]
     */
    #[ORM\Column(type: 'json')]
    private array $data = [ ];

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getIdpId(): ?UuidInterface {
        return $this->idpId;
    }

    public function setIdpId(UuidInterface $uuid): User {
        $this->idpId = $uuid;
        return $this;
    }

    public function getFirstname(): string {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): string {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static {
        $this->lastname = $lastname;
        return $this;
    }

    public function getEmail(): string {
        return $this->email;
    }


    public function setEmail(string $email): static {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array {
        return $this->roles;
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): static {
        $this->roles = $roles;

        return $this;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getUserIdentifier(): string {
        return $this->username;
    }

    public function setUsername($username): static {
        $this->username = $username;

        return $this;
    }

    public function getData(string $key, $default = null) {
        return $this->data[$key] ?? $default;
    }

    public function setData(string $key, $data): void {
        $this->data[$key] = $data;
    }

    // -------------------------------------------

    public function eraseCredentials() {
    }

    public function __toString(): string {
        if(empty($this->getFirstname()) && empty($this->getLastname())) {
            return $this->getUsername();
        }

        if(empty($this->getFirstname())) {
            return $this->getLastname();
        }

        if(empty($this->getLastname())) {
            return $this->getFirstname();
        }

        return sprintf('%s, %s', $this->getLastname(), $this->getFirstname());
    }

    public function __serialize(): array {
        return [
            'id' => $this->getId(),
            'username' => $this->username
        ];
    }

    public function __unserialize(array $data): void {
        $this->id = $data['id'];
        $this->username = $data['username'];
    }
}