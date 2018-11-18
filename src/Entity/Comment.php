<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="problem_comments", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 */
class Comment {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Problem", inversedBy="comments")
     * @ORM\JoinColumn(name="problem", referencedColumnName="id", onDelete="CASCADE")
     */
    private $problem;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * @Gedmo\Blameable(on="create")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", name="updated_at", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"content"})
     */
    private $updatedAt;

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
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