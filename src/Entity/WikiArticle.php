<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="wiki_articles", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"},
 *  indexes={
 *     @ORM\Index(columns={"name"}, flags={"fulltext"}),
 *     @ORM\Index(columns={"content"}, flags={"fulltext"})
 *  })
 */
class WikiArticle implements WikiAccessInterface {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="WikiCategory", inversedBy="articles")
     * @ORM\JoinColumn(name="category", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $category;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $content;

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
     * @Gedmo\Timestampable(on="change", field={"name", "contents"})
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id", nullable=true)
     * @Gedmo\Blameable(on="change", field={"name", "content"})
     */
    private $updatedBy;

    /**
     * @ORM\Column(type=WikiAccess::class)
     */
    private $access;

    public function __construct() {
        $this->access = WikiAccess::Inherit();
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return WikiCategory
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * @param WikiCategory $category
     * @return WikiArticle
     */
    public function setCategory(WikiCategory $category = null) {
        $this->category = $category;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return WikiArticle
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug() {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return WikiArticle
     */
    public function setSlug($slug) {
        $this->slug = $slug;
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
     * @return WikiArticle
     */
    public function setContent($content) {
        $this->content = $content;
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
     * @return User|null
     */
    public function getUpdatedBy() {
        return $this->updatedBy;
    }

    /**
     * @return string
     */
    public function getAccess() {
        return $this->access;
    }

    /**
     * @param WikiAccess $access
     * @return WikiArticle
     */
    public function setAccess(WikiAccess $access) {
        $this->access = $access;
        return $this;
    }
}
