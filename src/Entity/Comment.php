<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Comment {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Problem", inversedBy="comments")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $problem;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn()
     * @Gedmo\Blameable(on="create")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"content"})
     */
    private $updatedAt;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return Problem
     */
    public function getProblem() {
        return $this->problem;
    }

    /**
     * @param Problem $problem
     * @return Comment
     */
    public function setProblem(Problem $problem) {
        $this->problem = $problem;
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
     * @return Comment
     */
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    /**
     * @return User
     */
    public function getCreatedBy() {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     * @return Comment
     */
    public function setCreatedBy(User $createdBy) {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function setCreatedAt() {
        $this->createdAt = new \DateTime('now');
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function setUpdatedAt() {
        $this->updatedAt = new \DateTime('now');
    }
}