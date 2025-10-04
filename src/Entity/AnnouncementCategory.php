<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class AnnouncementCategory {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $name = null;

    /**
     * @var Collection<Announcement>
     */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Announcement::class)]
    private Collection $announcements;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->announcements = new ArrayCollection();
    }
    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): static {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<Announcement>
     */
    public function getAnnouncements(): Collection {
        return $this->announcements;
    }
}
