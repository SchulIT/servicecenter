<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="rooms", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @UniqueEntity(fields={"alias"})
 */
class Room {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="RoomCategory", inversedBy="rooms")
     * @ORM\JoinColumn(name="category", referencedColumnName="id", onDelete="CASCADE")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=32, unique=true, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(max="32")
     */
    private $alias;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Device", mappedBy="room")
     */
    private $devices;

    /**
     * @ORM\ManyToMany(targetEntity="Announcement", mappedBy="rooms")
     */
    private $announcements;

    public function __construct() {
        $this->devices = new ArrayCollection();
        $this->announcements = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return RoomCategory
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * @param RoomCategory $category
     * @return Room
     */
    public function setCategory(RoomCategory $category) {
        $this->category = $category;
        return $this;
    }

    /**
     * @return string
     */
    public function getAlias() {
        return $this->alias;
    }

    /**
     * @param string $alias
     * @return Room
     */
    public function setAlias($alias) {
        $this->alias = $alias;
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
     * @return Room
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getDevices() {
        return $this->devices;
    }

    /**
     * @return ArrayCollection
     */
    public function getAnnouncements() {
        return $this->announcements;
    }

    public function __toString() {
        return $this->getName();
    }
}