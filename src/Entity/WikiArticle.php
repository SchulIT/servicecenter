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
 * @ORM\Table(
 *     name="wiki",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="unique_parent_slug", columns={"parent", "slug"})
 *     },
 *     indexes={
 *         @ORM\Index(columns={"name"}, flags={"fulltext"}),
 *         @ORM\Index(columns={"content"}, flags={"fulltext"})
 *     }
 * )
 * @Gedmo\Tree(type="nested")
 */
class WikiArticle {

    use IdTrait;
    use UuidTrait;

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
     * @ORM\JoinColumn(onDelete="SET NULL")
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
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Gedmo\Blameable(on="change", field={"name", "content"})
     */
    private $updatedBy;

    /**
     * @ORM\Column(type="wiki_access")
     */
    private $access;

    /**
     * @Gedmo\TreeLeft()
     * @ORM\Column(type="integer", name="`left`")
     * @var int
     */
    private $left;

    /**
     * @Gedmo\TreeLevel()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $level;

    /**
     * @Gedmo\TreeRight()
     * @ORM\Column(type="integer", name="`right`")
     * @var int
     */
    private $right;

    /**
     * @Gedmo\TreeRoot()
     * @ORM\ManyToOne(targetEntity="WikiArticle")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var WikiArticle|null
     */
    private $root;

    /**
     * @Gedmo\TreeParent()
     * @ORM\ManyToOne(targetEntity="WikiArticle", inversedBy="children")
     * @ORM\JoinColumn(name="`parent`", onDelete="CASCADE")
     * @var WikiArticle|null
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="WikiArticle", mappedBy="parent")
     * @ORM\OrderBy({"name" = "ASC"})
     * @var Collection<WikiArticle>
     */
    private $children;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->access = WikiAccess::Inherit();

        $this->children = new ArrayCollection();
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
     * @return WikiAccess
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

    /**
     * @return WikiArticle|null
     */
    public function getRoot(): ?WikiArticle {
        return $this->root;
    }

    /**
     * @return WikiArticle|null
     */
    public function getParent(): ?WikiArticle {
        return $this->parent;
    }

    /**
     * @param WikiArticle|null $parent
     * @return WikiArticle
     */
    public function setParent(?WikiArticle $parent): WikiArticle {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return Collection<WikiArticle>
     */
    public function getChildren(): Collection {
        return $this->children;
    }
}
