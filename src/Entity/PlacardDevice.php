<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="placard_devices", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 */
class PlacardDevice {
    /**
     * @ORM\GeneratedValue()
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Placard", inversedBy="devices")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $placard;

    /**
     * @ORM\Column(type="string")
     */
    private $source;

    /**
     * @ORM\Column(type="string")
     */
    private $beamer;

    /**
     * @ORM\Column(type="string")
     */
    private $av;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return Placard
     */
    public function getPlacard() {
        return $this->placard;
    }

    /**
     * @param Placard $placard
     * @return PlacardDevice
     */
    public function setPlacard(Placard $placard) {
        $this->placard = $placard;
        return $this;
    }

    /**
     * @return string
     */
    public function getSource() {
        return $this->source;
    }

    /**
     * @param string $source
     * @return PlacardDevice
     */
    public function setSource($source) {
        $this->source = $source;
        return $this;
    }

    /**
     * @return string
     */
    public function getBeamer() {
        return $this->beamer;
    }

    /**
     * @param string $beamer
     * @return PlacardDevice
     */
    public function setBeamer($beamer) {
        $this->beamer = $beamer;
        return $this;
    }

    /**
     * @return string
     */
    public function getAv() {
        return $this->av;
    }

    /**
     * @param string $av
     * @return PlacardDevice
     */
    public function setAv($av) {
        $this->av = $av;
        return $this;
    }
}