<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *  indexes={
 *     @ORM\Index(columns={"name"}, flags={"fulltext"}),
 *     @ORM\Index(columns={"content"}, flags={"fulltext"})
 *  })
 */
class WikiArticle implements WikiAccessInterface {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="WikiCategory", inversedBy="articles")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $category;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn()
     * @Gedmo\Blameable(on="create")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"name", "contents"})
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn()
     * @Gedmo\Blameable(on="change", field={"name", "content"})
     */
    private $updatedBy;

    /**
     * @ORM\Column(type="wiki_access")
     */
    private $access;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->access = WikiAccess::Inherit();
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
