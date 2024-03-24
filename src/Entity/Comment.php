<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Comment {

    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: Problem::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Problem $problem;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Gedmo\Blameable(on: 'create')]
    private ?User $createdBy = null;

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    private ?DateTime $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Gedmo\Timestampable(on: 'change', field: ['content'])]
    private ?DateTime $updatedAt = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getProblem(): Problem {
        return $this->problem;
    }

    public function setProblem(Problem $problem): self {
        $this->problem = $problem;
        return $this;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(string $content): self {
        $this->content = $content;
        return $this;
    }

    public function getCreatedBy(): ?User {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): static {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getCreatedAt(): ?DateTime {
        return $this->createdAt;
    }

    public function setCreatedAt(): void {
        $this->createdAt = new DateTime('now');
    }

    public function getUpdatedAt(): ?DateTime {
        return $this->updatedAt;
    }

    public function setUpdatedAt(): void {
        $this->updatedAt = new DateTime('now');
    }
}