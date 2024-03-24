<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[UniqueEntity(fields: ['name'])]
class Application implements UserInterface {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    #[Assert\Length(max: 64)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    #[Assert\NotNull]
    private ?string $apiKey = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $lastActivity = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): static {
        $this->name = $name;
        return $this;
    }

    public function getApiKey(): ?string {
        return $this->apiKey;
    }

    public function setApiKey(?string $apiKey): static {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function getLastActivity(): ?DateTime {
        return $this->lastActivity;
    }

    public function setLastActivity(?DateTime $lastActivity): static {
        $this->lastActivity = $lastActivity;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoles(): array {
        return [ 'ROLE_API' ];
    }

    public function getUsername(): ?string {
        return $this->name;
    }

    public function getUserIdentifier(): string {
        return $this->getName();
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials() { }
}