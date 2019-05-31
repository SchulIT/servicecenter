<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Announcement {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AnnouncementCategory", inversedBy="announcements")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Blameable(on="create")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $details;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date()
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\Date()
     */
    private $endDate;

    /**
     * @ORM\ManyToMany(targetEntity="Room", inversedBy="announcements")
     * @ORM\JoinTable()
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
     * @return AnnouncementCategory
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * @param AnnouncementCategory $category
     * @return Announcement
     */
    public function setCategory(AnnouncementCategory $category) {
        $this->category = $category;
        return $this;
    }

    /**
     * @return User
     */
    public function getCreatedBy() {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     * @return Announcement
     */
    public function setCreatedBy(User $createdBy) {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Announcement
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDetails() {
        return $this->details;
    }

    /**
     * @param string $details
     * @return Announcement
     */
    public function setDetails($details) {
        $this->details = $details;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     * @return Announcement
     */
    public function setStartDate(\DateTime $startDate) {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     * @return Announcement
     */
    public function setEndDate(\DateTime $endDate = null) {
        $this->endDate = $endDate;
        return $this;
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
    public function getRooms() {
        return $this->rooms;
    }
}