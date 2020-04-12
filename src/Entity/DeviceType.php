<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class DeviceType {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Device", mappedBy="type")
     */
    private $devices;

    /**
     * @ORM\OneToMany(targetEntity="ProblemType", mappedBy="deviceType")
     */
    private $problemTypes;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->devices = new ArrayCollection();
        $this->problemTypes = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return DeviceType
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getDevices() {
        return $this->devices;
    }

    /**
     * @return ArrayCollection
     */
    public function getProblemTypes() {
        return $this->problemTypes;
    }

    public function __toString() {
        return $this->getName();
    }
}