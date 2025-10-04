<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[Gedmo\Loggable]
class Problem {

    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: ProblemType::class, inversedBy: 'problems')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Gedmo\Versioned]
    private ?ProblemType $problemType = null;

    #[ORM\Column(type: 'string', enumType: Priority::class)]
    #[Gedmo\Versioned]
    private Priority $priority = Priority::Normal;
    
    #[ORM\Column(type: 'boolean')]
    #[Gedmo\Versioned]
    private bool $isOpen = true;
    
    #[ORM\Column(type: 'boolean')]
    #[Gedmo\Versioned]
    private bool $isMaintenance = false;
    
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Gedmo\Versioned]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Gedmo\Versioned]
    private ?User $assignee = null;

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTime $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Gedmo\Blameable(on: 'create')]
    private ?User $createdBy = null;

    #[Gedmo\Timestampable(on: 'update', field: ['priority', 'isOpen', 'isMaintenance', 'content', 'assignee'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Device::class, inversedBy: 'problems')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Device $device;

    /**
     * @var Collection<Comment>
     */
    #[ORM\OneToMany(mappedBy: 'problem', targetEntity: Comment::class)]
    private Collection $comments;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->comments = new ArrayCollection();
    }

    public function getProblemType(): ?ProblemType {
        return $this->problemType;
    }

    public function setProblemType(ProblemType $problemType): static {
        $this->problemType = $problemType;
        return $this;
    }

    public function getPriority(): Priority {
        return $this->priority;
    }

    public function setPriority(Priority $priority): static {
        $this->priority = $priority;
        return $this;
    }

    public function isOpen(): bool {
        return $this->isOpen;
    }

    public function setIsOpen(bool $isOpen): static {
        $this->isOpen = $isOpen;
        return $this;
    }

    public function isMaintenance(): bool {
        return $this->isMaintenance;
    }

    public function setIsMaintenance(bool $isMaintenance): static {
        $this->isMaintenance = $isMaintenance;
        return $this;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(string $content): static {
        $this->content = $content;
        return $this;
    }

    public function getAssignee(): ?User {
        return $this->assignee;
    }

    public function setAssignee(?User $assignee = null): static {
        $this->assignee = $assignee;
        return $this;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function getCreatedBy(): ?User {
        return $this->createdBy;
    }

    public function getUpdatedAt(): ?DateTime {
        return $this->updatedAt;
    }

    public function getDevice(): Device {
        return $this->device;
    }

    public function setDevice(Device $device): static {
        $this->device = $device;
        return $this;
    }

    /**
     * @return Collection<Comment>
     */
    public function getComments(): Collection {
        return $this->comments;
    }

    public function addComment(Comment $comment): void {
        $this->comments->add($comment);
    }

    public function removeComment(Comment $comment): void {
        $this->comments->removeElement($comment);
    }
}
