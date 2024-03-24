<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Stringable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class DeviceType implements Stringable {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * @var Collection<Device>
     */
    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Device::class)]
    private Collection $devices;

    /**
     * @var Collection<ProblemType>
     */
    #[ORM\OneToMany(mappedBy: 'deviceType', targetEntity: ProblemType::class)]
    private Collection $problemTypes;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->devices = new ArrayCollection();
        $this->problemTypes = new ArrayCollection();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): static {
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
     * @return Collection<ProblemType>
     */
    public function getProblemTypes(): Collection {
        return $this->problemTypes;
    }

    public function __toString(): string {
        return $this->getName();
    }
}