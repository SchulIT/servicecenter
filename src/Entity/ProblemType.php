<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class ProblemType {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="DeviceType", inversedBy="problemTypes")
     * @ORM\JoinColumn()
     */
    private $deviceType;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Problem", mappedBy="problemType")
     */
    private $problems;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->problems = new ArrayCollection();
    }

    /**
     * @return DeviceType
     */
    public function getDeviceType() {
        return $this->deviceType;
    }

    /**
     * @param DeviceType $deviceType
     * @return ProblemType
     */
    public function setDeviceType(DeviceType $deviceType) {
        $this->deviceType = $deviceType;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ProblemType
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getProblems() {
        return $this->problems;
    }

    public function __toString() {
        return $this->getName();
    }
}