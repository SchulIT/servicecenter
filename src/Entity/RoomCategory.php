<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class RoomCategory {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Room", mappedBy="category", cascade={"persist"})
     */
    private $rooms;

    public function __construct() {
        $this->rooms = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return RoomCategory
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getRooms() {
        return $this->rooms;
    }

    /**
     * @param Room $room
     */
    public function addRoom(Room $room) {
        $this->rooms->add($room);
    }

    /**
     * @param Room $room
     */
    public function removeRoom(Room $room) {
        $this->rooms->removeElement($room);
    }

    public function __toString() {
        return $this->getName();
    }
}