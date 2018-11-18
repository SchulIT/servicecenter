<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="devices", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 */
class Device {
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
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="devices")
     * @ORM\JoinColumn(name="room", referencedColumnName="id", onDelete="CASCADE")
     */
    private $room;

    /**
     * @ORM\ManyToOne(targetEntity="DeviceType", inversedBy="devices")
     * @ORM\JoinColumn(name="type", referencedColumnName="id", onDelete="CASCADE")
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="Problem", mappedBy="device")
     */
    private $problems;

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
     * @return Device
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Room
     */
    public function getRoom() {
        return $this->room;
    }

    /**
     * @param Room $room
     * @return Device
     */
    public function setRoom(Room $room) {
        $this->room = $room;
        return $this;
    }

    /**
     * @return DeviceType
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param DeviceType $type
     * @return Device
     */
    public function setType(DeviceType $type) {
        $this->type = $type;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getProblems() {
        return $this->problems;
    }

    public function __toString() {
        return $this->getName();
    }
}