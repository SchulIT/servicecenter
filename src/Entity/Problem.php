<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="problems", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 */
class Problem {
    const STATUS_OPEN = 0;
    const STATUS_DOING = 1;
    const STATUS_SOLVED = 2;

    const PRIORITY_CRITICAL = 3;
    const PRIORITY_HIGH = 2;
    const PRIORITY_NORMAL = 1;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ProblemType", inversedBy="problems")
     * @ORM\JoinColumn(name="problem_type", referencedColumnName="id", onDelete="CASCADE")
     */
    private $problemType;

    /**
     * @ORM\Column(type="integer")
     */
    private $priority;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="boolean", name="is_maintenance")
     */
    private $isMaintenance = false;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="contact_person", referencedColumnName="id", nullable=true)
     */
    private $contactPerson;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * @Gedmo\Blameable(on="create")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="datetime", name="updated_at", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"priority", "status", "isMaintenance", "content", "contactPerson"})
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Device", inversedBy="problems")
     * @ORM\JoinColumn(name="device", referencedColumnName="id", onDelete="CASCADE")
     */
    private $device;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="problem")
     */
    private $comments;

    public function __construct() {
        $this->comments = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
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
     * @return int
     */
    public function getPriority() {
        return $this->priority;
    }

    /**
     * @param int $priority
     * @return Problem
     */
    public function setPriority($priority) {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Problem
     */
    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isMaintenance() {
        return $this->isMaintenance;
    }

    /**
     * @param bool $isMaintenance
     * @return Problem
     */
    public function setIsMaintenance($isMaintenance) {
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
    public function getContactPerson() {
        return $this->contactPerson;
    }

    /**
     * @param User $contactPerson
     * @return Problem
     */
    public function setContactPerson(User $contactPerson = null) {
        $this->contactPerson = $contactPerson;
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

    /**
     * @param Device $device
     * @return Problem
     */
    public function setDevice(Device $device) {
        $this->device = $device;
        return $this;
    }

    /**
     * @return ArrayCollection
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