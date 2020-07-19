<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Gedmo\Loggable()
 */
class Problem {
    const PRIORITY_CRITICAL = 3;
    const PRIORITY_HIGH = 2;
    const PRIORITY_NORMAL = 1;

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="ProblemType", inversedBy="problems")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Gedmo\Versioned()
     */
    private $problemType;

    /**
     * @ORM\Column(type="priority")
     * @Gedmo\Versioned()
     */
    private $priority;

    /**
     * @ORM\Column(type="boolean")
     * @Gedmo\Versioned()
     */
    private $isOpen = true;

    /**
     * @ORM\Column(type="boolean")
     * @Gedmo\Versioned()
     */
    private $isMaintenance = false;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Gedmo\Versioned()
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Gedmo\Versioned()
     */
    private $assignee;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Gedmo\Blameable(on="create")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     * @Gedmo\Timestampable(on="change", field={"priority", "isOpen", "isMaintenance", "content", "assignee"})
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Device", inversedBy="problems")
     * @ORM\JoinColumn()
     */
    private $device;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="problem")
     */
    private $comments;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->comments = new ArrayCollection();

        $this->priority = Priority::Normal();
    }

    /**
     * @return ProblemType
     */
    public function getProblemType() {
        return $this->problemType;
    }

    /**
     * @param ProblemType $problemType
     * @return Problem
     */
    public function setProblemType(ProblemType $problemType) {
        $this->problemType = $problemType;
        return $this;
    }

    /**
     * @return Priority
     */
    public function getPriority(): Priority {
        return $this->priority;
    }

    /**
     * @param Priority $priority
     * @return Problem
     */
    public function setPriority(Priority $priority) {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return bool
     */
    public function isOpen(): bool {
        return $this->isOpen;
    }

    /**
     * @param bool $isOpen
     * @return Problem
     */
    public function setIsOpen(bool $isOpen) {
        $this->isOpen = $isOpen;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isMaintenance(): bool {
        return $this->isMaintenance;
    }

    /**
     * @param bool $isMaintenance
     * @return Problem
     */
    public function setIsMaintenance(bool $isMaintenance) {
        $this->isMaintenance = $isMaintenance;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Problem
     */
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getAssignee() {
        return $this->assignee;
    }

    /**
     * @param User $assignee
     * @return Problem
     */
    public function setAssignee(User $assignee = null) {
        $this->assignee = $assignee;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * @return User
     */
    public function getCreatedBy() {
        return $this->createdBy;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * @return Device
     */
    public function getDevice() {
        return $this->device;
    }

    public function setDevice($device) {
        $this->device = $device;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getComments() {
        return $this->comments;
    }

    /**
     * @param Comment $comment
     */
    public function addComment(Comment $comment) {
        $this->comments->add($comment);
    }

    /**
     * @param Comment $comment
     */
    public function removeComment(Comment $comment) {
        $this->comments->removeElement($comment);
    }
}