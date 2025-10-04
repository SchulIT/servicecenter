<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class ProblemFilter {

    use IdTrait;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE', nullable: false)]
    private User $user;

    /**
     * @var Collection<Room>
     */
    #[ORM\JoinTable]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: Room::class)]
    private Collection $rooms;

    #[ORM\Column(type: 'boolean')]
    private bool $includeSolved = false;

    #[ORM\Column(type: 'boolean')]
    private bool $includeMaintenance = true;

    #[ORM\Column(type: 'string')]
    #[Assert\Choice(choices: ['createdAt', 'updatedAt', 'priority'])]
    private string $sortColumn = 'updatedAt';

    #[ORM\Column(type: 'string')]
    #[Assert\Choice(choices: ['asc', 'desc'])]
    private string $sortOrder = 'desc';

    #[ORM\Column(type: 'integer')]
    #[Assert\Choice(choices: [15, 25, 50, 75, 100])]
    private int $numItems = 25;

    public function __construct() {
        $this->rooms = new ArrayCollection();
    }


    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $user): static {
        $this->user = $user;
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

    public function getIncludeSolved(): bool {
        return $this->includeSolved;
    }

    public function setIncludeSolved(bool $includeSolved): static {
        $this->includeSolved = $includeSolved;
        return $this;
    }

    public function getIncludeMaintenance(): bool {
        return $this->includeMaintenance;
    }

    public function setIncludeMaintenance(bool $includeMaintenance): static {
        $this->includeMaintenance = $includeMaintenance;
        return $this;
    }

    public function getSortColumn(): string {
        return $this->sortColumn;
    }

    public function setSortColumn(string $sortColumn): static {
        $this->sortColumn = $sortColumn;
        return $this;
    }

    public function getSortOrder(): string {
        return $this->sortOrder;
    }

    public function setSortOrder(string $sortOrder): static {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    public function getNumItems(): int {
        return $this->numItems;
    }

    public function setNumItems(int $numItems): static {
        $this->numItems = $numItems;
        return $this;
    }

    public function isDefaultFilter(): bool {
        $defaultFilter = new self();

        return $this->getRooms()->count() === 0
            && $this->getIncludeSolved() === $defaultFilter->getIncludeSolved()
            && $this->getIncludeMaintenance() === $defaultFilter->getIncludeMaintenance()
            && $this->getSortColumn() === $defaultFilter->getSortColumn()
            && $this->getSortOrder() === $defaultFilter->getSortOrder()
            && $this->getNumItems() === $defaultFilter->getNumItems();
    }
}
