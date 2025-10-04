<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Announcement {

    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: AnnouncementCategory::class, inversedBy: 'announcements')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private AnnouncementCategory $category;


    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Gedmo\Blameable(on: 'create')]
    private User $createdBy;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $details = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTime $startDate = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\GreaterThan(propertyPath: 'startDate')]
    private ?DateTime $endDate = null;

    /**
     * @var Collection<Room>
     */
    #[ORM\ManyToMany(targetEntity: Room::class, inversedBy: 'announcements')]
    #[ORM\JoinTable]
    private Collection $rooms;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->rooms = new ArrayCollection();
    }

    public function getCategory(): AnnouncementCategory {
        return $this->category;
    }

    public function setCategory(AnnouncementCategory $category): static {
        $this->category = $category;
        return $this;
    }

    public function getCreatedBy(): ?User {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): static {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(string $title): static {
        $this->title = $title;
        return $this;
    }

    public function getDetails(): ?string {
        return $this->details;
    }

    public function setDetails(?string $details): static {
        $this->details = $details;
        return $this;
    }

    public function getStartDate(): ?DateTime {
        return $this->startDate;
    }

    public function setStartDate(DateTime $startDate): static {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?DateTime {
        return $this->endDate;
    }

    public function setEndDate(DateTime $endDate = null): static {
        $this->endDate = $endDate;
        return $this;
    }

    public function addRoom(Room $room): void {
        $this->rooms->add($room);
    }

    public function removeRoom(Room $room): void {
        $this->rooms->removeElement($room);
    }

    /**
     * @return Collection<Room>
     */
    public function getRooms(): Collection {
        return $this->rooms;
    }
}
