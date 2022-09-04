<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Serializable;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class User implements UserInterface, Serializable {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="uuid")
     * @var UuidInterface
     */
    private $idpId;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $firstname;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @ORM\Column(type="string")
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [ 'ROLE_USER' ];

    /**
     * @ORM\Column(type="json")
     * @var string[]
     */
    private $data = [ ];

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return UuidInterface|null
     */
    public function getIdpId(): ?UuidInterface {
        return $this->idpId;
    }

    /**
     * @param UuidInterface $uuid
     * @return User
     */
    public function setIdpId(UuidInterface $uuid): User {
        $this->idpId = $uuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail($email) {
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
     * @return User
     */
    public function setRoles(array $roles) {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    public function getUserIdentifier(): string {
        return $this->username;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername($username) {
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

    /**
     * @return string
     */
    public function getPassword(): ?string {
        return null;
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string {
        return null;
    }

    public function eraseCredentials() {
    }

    public function __toString() {
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

    /**
     * @inheritDoc
     */
    public function serialize() {
        return serialize([
            $this->getId(),
            $this->getUsername()
        ]);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized) {
        list($this->id, $this->username) = unserialize($serialized);
    }
}