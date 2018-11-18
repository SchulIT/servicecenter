<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class User implements UserInterface {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @ORM\Column(type="json_array")
     */
    private $roles = [ 'ROLE_USER' ];

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
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
    public function getRoles() {
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

    /**
     * @param string $username
     * @return User
     */
    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }

    // -------------------------------------------

    /**
     * @return string
     */
    public function getPassword() {
        return null;
    }

    /**
     * @return string|null
     */
    public function getSalt() {
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
}