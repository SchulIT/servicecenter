<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class NotificationSetting {
    /**
     * @ORM\GeneratedValue()
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn()
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isEnabled = false;

    /**
     * @ORM\ManyToMany(targetEntity="Room")
     * @ORM\JoinTable()
     */
    private $rooms;

    /**
     * @ORM\ManyToMany(targetEntity="ProblemType")
     * @ORM\JoinTable()
     */
    private $problemTypes;

    public function __construct() {
        $this->rooms = new ArrayCollection();
        $this->problemTypes = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param User $user
     * @return NotificationSetting
     */
    public function setUser(User $user) {
        $this->user = $user;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled() {
        return $this->isEnabled;
    }

    /**
     * @param bool $isEnabled
     * @return NotificationSetting
     */
    public function setIsEnabled($isEnabled) {
        $this->isEnabled = $isEnabled;
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

    /**
     * @return ArrayCollection
     */
    public function getProblemTypes() {
        return $this->problemTypes;
    }

    /**
     * @param ProblemType $type
     */
    public function addProblemType(ProblemType $type) {
        $this->problemTypes->add($type);
    }

    /**
     * @param ProblemType $type
     */
    public function removeProblemType(ProblemType $type) {
        $this->problemTypes->removeElement($type);
    }
}