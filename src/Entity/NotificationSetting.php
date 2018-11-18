<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="notification_settings", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
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
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean", name="is_enabled")
     */
    private $isEnabled = true;

    /**
     * @ORM\Column(type="text")
     */
    private $email;

    /**
     * @ORM\ManyToMany(targetEntity="Room")
     * @ORM\JoinTable(name="notification_setting_rooms",
     *     joinColumns={@ORM\JoinColumn(name="setting", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="room", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private $rooms;

    /**
     * @ORM\ManyToMany(targetEntity="ProblemType")
     * @ORM\JoinTable(name="notification_setting_problemtypes",
     *     joinColumns={@ORM\JoinColumn(name="setting", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="problemtype", referencedColumnName="id", onDelete="CASCADE")}
     * )
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
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string $email
     * @return NotificationSetting
     */
    public function setEmail($email) {
        $this->email = $email;
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