<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Room {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="RoomCategory", inversedBy="rooms")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $category;

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
        $this->uuid = Uuid::uuid4();

        $this->devices = new ArrayCollection();
        $this->announcements = new ArrayCollection();
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