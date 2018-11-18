<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="announcement_categories", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 */
class AnnouncementCategory {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Announcement", mappedBy="category")
     */
    private $announcements;

    public function __construct() {
        $this->announcements = new ArrayCollection();
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
     * @return AnnouncementCategory
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAnnouncements() {
        return $this->announcements;
    }
}