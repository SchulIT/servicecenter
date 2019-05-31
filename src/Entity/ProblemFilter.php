<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class ProblemFilter {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn()
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Room")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $room = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private $includeSolved = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $includeMaintenance = true;

    /**
     * @ORM\Column(type="string")
     * @Assert\Choice(choices={"createdAt", "updatedAt", "status", "priority", "room"})
     */
    private $sortColumn = 'updatedAt';

    /**
     * @ORM\Column(type="string")
     * @Assert\Choice(choices={"asc", "desc"})
     */
    private $sortOrder = 'asc';

    /**
     * @ORM\Column(type="integer")
     * @Assert\Choice(choices={15, 25, 50, 75, 100})
     */
    private $numItems = 25;

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
     * @return ProblemFilter
     */
    public function setUser(User $user) {
        $this->user = $user;
        return $this;
    }

    /**
     * @return null|Room
     */
    public function getRoom() {
        return $this->room;
    }

    /**
     * @param null|Room $room
     * @return ProblemFilter
     */
    public function setRoom(Room $room = null) {
        $this->room = $room;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIncludeSolved() {
        return $this->includeSolved;
    }

    /**
     * @param bool $includeSolved
     * @return ProblemFilter
     */
    public function setIncludeSolved(bool $includeSolved) {
        $this->includeSolved = $includeSolved;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIncludeMaintenance() {
        return $this->includeMaintenance;
    }

    /**
     * @param bool $includeMaintenance
     * @return ProblemFilter
     */
    public function setIncludeMaintenance(bool $includeMaintenance) {
        $this->includeMaintenance = $includeMaintenance;
        return $this;
    }

    /**
     * @return string
     */
    public function getSortColumn() {
        return $this->sortColumn;
    }

    /**
     * @param string $sortColumn
     * @return ProblemFilter
     */
    public function setSortColumn(string $sortColumn) {
        $this->sortColumn = $sortColumn;
        return $this;
    }

    /**
     * @return string
     */
    public function getSortOrder() {
        return $this->sortOrder;
    }

    /**
     * @param string $sortOrder
     * @return ProblemFilter
     */
    public function setSortOrder(string $sortOrder) {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumItems() {
        return $this->numItems;
    }

    /**
     * @param int $numItems
     * @return ProblemFilter
     */
    public function setNumItems(int $numItems) {
        $this->numItems = $numItems;
        return $this;
    }

    public function isDefaultFilter() {
        $defaultFilter = new static();

        return $this->getRoom() === $defaultFilter->getRoom()
            && $this->getIncludeSolved() === $defaultFilter->getIncludeSolved()
            && $this->getIncludeMaintenance() === $defaultFilter->getIncludeMaintenance()
            && $this->getSortColumn() === $this->getSortColumn()
            && $this->getSortOrder() === $this->getSortOrder()
            && $this->getNumItems() === $this->getNumItems();
    }
}