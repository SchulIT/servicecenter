<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity]
#[ORM\Table(name: 'wiki')]
#[ORM\Index(columns: ['name'], flags: ['fulltext'])]
#[ORM\Index(columns: ['content'], flags: ['fulltext'])]
#[ORM\UniqueConstraint(name: 'unique_parent_slug', columns: ['parent', 'slug'])]
#[Gedmo\Tree(type: 'nested')]
class WikiArticle {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: 'string')]
    #[Gedmo\Slug(fields: ['name'])]
    private ?string $slug = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $content = null;

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTime $createdAt;

    /**
     * @var Collection<WikiArticle>
     */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: WikiArticle::class)]
    #[ORM\OrderBy(['name' => 'ASC'])]
    private Collection $children;

    #[ORM\ManyToOne(targetEntity: 'User')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Gedmo\Blameable(on: 'create')]
    private ?User $createdBy = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Gedmo\Timestampable(on: 'change', field: ['name', 'contents'])]
    private ?DateTime $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: 'User')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Gedmo\Blameable(on: 'change', field: ['name', 'contents'])]
    private ?User $updatedBy = null;

    #[ORM\Column(type: 'string', enumType: WikiAccess::class)]
    private WikiAccess $access;

    #[ORM\Column(name: '`left`', type: 'integer')]
    #[Gedmo\TreeLeft]
    private int $left;


    #[ORM\Column(type: 'integer')]
    #[Gedmo\TreeLevel]
    private int $level;

    #[ORM\Column(name: '`right`', type: 'integer')]
    #[Gedmo\TreeRight]
    private int $right;

    #[ORM\ManyToOne(targetEntity: WikiArticle::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Gedmo\TreeRoot]
    private ?WikiArticle $root = null;

    #[ORM\ManyToOne(targetEntity: WikiArticle::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: '`parent`', onDelete: 'CASCADE')]
    #[Gedmo\TreeParent]
    #[Assert\NotNull]
    private ?WikiArticle $parent = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->access = WikiAccess::Inherit;

        $this->children = new ArrayCollection();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): static {
        $this->name = $name;
        return $this;
    }

    public function getSlug(): ?string {
        return $this->slug;
    }

    public function setSlug(string $slug): static {
        $this->slug = $slug;
        return $this;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(string $content): static {
        $this->content = $content;
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

    public function getUpdatedBy(): ?User {
        return $this->updatedBy;
    }

    public function getAccess(): WikiAccess {
        return $this->access;
    }

    public function setAccess(WikiAccess $access): static {
        $this->access = $access;
        return $this;
    }

    public function getRoot(): ?WikiArticle {
        return $this->root;
    }

    public function getParent(): ?WikiArticle {
        return $this->parent;
    }

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
