<?php

declare(strict_types=1);

namespace App\Entity;

use Override;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Device implements Stringable {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: Room::class, inversedBy: 'devices')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Room $room;

    #[ORM\ManyToOne(targetEntity: DeviceType::class, inversedBy: 'devices')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private DeviceType $type;

    /**
     * @var Collection<Problem>
     */
    #[ORM\OneToMany(mappedBy: 'device', targetEntity: Problem::class)]
    private Collection $problems;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->problems = new ArrayCollection();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): static {
        $this->name = $name;
        return $this;
    }

    public function getRoom(): Room {
        return $this->room;
    }

    public function setRoom(Room $room): static {
        $this->room = $room;
        return $this;
    }

    public function getType(): DeviceType {
        return $this->type;
    }

    public function setType(DeviceType $type): static {
        $this->type = $type;
        return $this;
    }

    /**
     * @return Collection<Problem>
     */
    public function getProblems(): Collection {
        return $this->problems;
    }

    #[Override]
    public function __toString(): string {
        return (string) $this->getName();
    }
}
