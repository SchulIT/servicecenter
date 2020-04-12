<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Entity()
 */
class WikiCategory implements WikiAccessInterface {

    use IdTrait;
    use UuidTrait;

    /**
     * @ManyToOne(targetEntity="WikiCategory", inversedBy="categories")
     * @JoinColumn(onDelete="CASCADE")
     */
    private $parent;

    /**
     * @Column(type="string")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @Column(type="string")
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * @OneToMany(targetEntity="WikiCategory", mappedBy="parent")
     */
    private $categories;

    /**
     * @OneToMany(targetEntity="WikiArticle", mappedBy="category")
     */
    private $articles;

    /**
     * @Column(type="wiki_access")
     */
    private $access;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->categories = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->access = WikiAccess::Inherit();
    }

    /**
     * @return WikiCategory|null
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * @param WikiCategory|null $parent
     * @return WikiCategory
     */
    public function setParent(WikiCategory $parent = null) {
        $this->parent = $parent;
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
     * @return WikiCategory
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
     * @return WikiCategory
     */
    public function setSlug($slug) {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCategories() {
        return $this->categories;
    }

    /**
     * @param WikiCategory $category
     */
    public function addCategory(WikiCategory $category) {
        $this->categories->add($category);
    }

    /**
     * @param WikiCategory $category
     */
    public function removeCategory(WikiCategory $category) {
        $this->categories->removeElement($category);
    }

    /**
     * @return ArrayCollection
     */
    public function getArticles() {
        return $this->articles;
    }

    /**
     * @param WikiArticle $article
     */
    public function addArticle(WikiArticle $article) {
        $this->articles->add($article);
    }

    /**
     * @param WikiArticle $article
     */
    public function removeArticle(WikiArticle $article) {
        $this->articles->removeElement($article);
    }

    /**
     * @return string
     */
    public function getAccess() {
        return $this->access;
    }

    /**
     * @param WikiAccess $access
     * @return WikiCategory
     */
    public function setAccess(WikiAccess $access) {
        $this->access = $access;
        return $this;
    }

}