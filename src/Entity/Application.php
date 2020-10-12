<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"name"})
 */
class Application implements UserInterface {

    use IdTrait;
    use UuidTrait;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=64, unique=true)
     * @Assert\Length(max="64")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     * @Assert\NotNull()
     * @var string|null
     */
    private $apiKey;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    private $lastActivity;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Application
     */
    public function setName(?string $name): Application {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getApiKey(): ?string {
        return $this->apiKey;
    }

    /**
     * @param string|null $apiKey
     * @return Application
     */
    public function setApiKey(?string $apiKey): Application {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getLastActivity(): ?DateTime {
        return $this->lastActivity;
    }

    /**
     * @param DateTime|null $lastActivity
     * @return Application
     */
    public function setLastActivity(?DateTime $lastActivity): Application {
        $this->lastActivity = $lastActivity;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoles() {
        return [ 'ROLE_API' ];
    }

    /**
     * @inheritDoc
     */
    public function getPassword() {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getSalt() {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getUsername() {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials() { }
}