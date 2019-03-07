<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity()
 * @ORM\Table(name="placards", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 */
class Placard {
    /**
     * @ORM\GeneratedValue()
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Room")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $room;

    /**
     * @ORM\Column(type="string")
     */
    private $header;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(referencedColumnName="id")
     * @Gedmo\Blameable(on="create")
     * @Gedmo\Blameable(on="update")
     */
    private $updatedBy;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="PlacardDevice", mappedBy="placard", cascade={"persist"})
     */
    private $devices;

    public function __construct() {
        $this->devices = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return Room
     */
    public function getRoom() {
        return $this->room;
    }

    /**
     * @param Room $room
     * @return Placard
     */
    public function setRoom(Room $room) {
        $this->room = $room;
        return $this;
    }

    /**
     * @return string
     */
    public function getHeader() {
        return $this->header;
    }

    /**
     * @param string $header
     * @return Placard
     */
    public function setHeader($header) {
        $this->header = $header;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getDevices() {
        return $this->devices;
    }

    /**
     * @param PlacardDevice $device
     */
    public function addDevice(PlacardDevice $device) {
        $this->devices->add($device);
        $device->setPlacard($this);
    }

    /**
     * @param PlacardDevice $device
     */
    public function removeDevice(PlacardDevice $device) {
        $this->devices->removeElement($device);
    }

    /**
     * @param User $user
     * @return Placard
     */
    public function setUpdatedBy(User $user) {
        $this->updatedBy = $user;
        return $this;
    }

    /**
     * @return User
     */
    public function getUpdatedBy() {
        return $this->updatedBy;
    }

    public function setUpdatedAt() {
        $this->updatedAt = new \DateTime('now');
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }
}