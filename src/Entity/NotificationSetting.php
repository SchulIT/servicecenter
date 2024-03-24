<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class NotificationSetting {

    use IdTrait;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE', nullable: false)]
    private User $user;

    #[ORM\Column(type: 'boolean')]
    private bool $isEnabled = false;

    /**
     * @var Collection<Room>
     */
    #[ORM\ManyToMany(targetEntity: Room::class)]
    #[ORM\JoinTable]
    private Collection $rooms;

    /**
     * @var Collection<ProblemType>
     */
    #[ORM\ManyToMany(targetEntity: ProblemType::class)]
    #[ORM\JoinTable]
    private Collection $problemTypes;

    public function __construct() {
        $this->rooms = new ArrayCollection();
        $this->problemTypes = new ArrayCollection();
    }

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $user): static {
        $this->user = $user;
        return $this;
    }

    public function isEnabled(): bool {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): static {
        $this->isEnabled = $isEnabled;
        return $this;
    }

    /**
     * @return Collection<Room>
     */
    public function getRooms(): Collection {
        return $this->rooms;
    }

    public function addRoom(Room $room): void {
        $this->rooms->add($room);
    }

    public function removeRoom(Room $room): void {
        $this->rooms->removeElement($room);
    }

    /**
     * @return Collection<ProblemType>
     */
    public function getProblemTypes(): Collection {
        return $this->problemTypes;
    }

    public function addProblemType(ProblemType $type): void {
        $this->problemTypes->add($type);
    }

    public function removeProblemType(ProblemType $type): void {
        $this->problemTypes->removeElement($type);
    }
}