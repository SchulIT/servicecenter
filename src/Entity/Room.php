<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Room implements Stringable {

    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: RoomCategory::class, inversedBy: 'rooms')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?RoomCategory $category = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * @var Collection<Device>
     */
    #[ORM\OneToMany(mappedBy: 'room', targetEntity: Device::class)]
    private Collection $devices;

    /**
     * @var Collection<Announcement>
     */
    #[ORM\ManyToMany(targetEntity: Announcement::class, mappedBy: 'rooms')]
    private Collection $announcements;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->devices = new ArrayCollection();
        $this->announcements = new ArrayCollection();
    }

    public function getCategory(): ?RoomCategory {
        return $this->category;
    }

    public function setCategory(RoomCategory $category): static {
        $this->category = $category;
        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): static {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<Device>
     */
    public function getDevices(): Collection {
        return $this->devices;
    }

    /**
     * @return Collection<Announcement>
     */
    public function getAnnouncements(): Collection {
        return $this->announcements;
    }

    public function __toString(): string {
        return $this->getName();
    }
}