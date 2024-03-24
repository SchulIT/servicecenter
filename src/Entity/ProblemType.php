<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Stringable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class ProblemType implements Stringable {

    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: DeviceType::class, inversedBy: 'problemTypes')]
    #[ORM\JoinColumn]
    #[Assert\NotNull]
    private ?DeviceType $deviceType = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * @var Collection<Problem>
     */
    #[ORM\OneToMany(mappedBy: 'problemType', targetEntity: Problem::class)]
    private Collection $problems;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->problems = new ArrayCollection();
    }

    public function getDeviceType(): ?DeviceType {
        return $this->deviceType;
    }

    public function setDeviceType(DeviceType $deviceType): static {
        $this->deviceType = $deviceType;
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
     * @return Collection<Problem>
     */
    public function getProblems(): Collection {
        return $this->problems;
    }

    public function __toString(): string {
        return $this->getName();
    }
}