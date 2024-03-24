<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Stringable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class RoomCategory implements Stringable {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * @var Collection<Room>
     */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Room::class, cascade: ['persist'])]
    private Collection $rooms;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->rooms = new ArrayCollection();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): static {
        $this->name = $name;
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

    public function __toString(): string {
        return $this->getName();
    }
}