<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

trait UuidTrait {
    /**
     * @var UuidInterface
     */
    #[ORM\Column(type: 'uuid', unique: true)]
    private $uuid;

    public function getUuid(): UuidInterface {
        return $this->uuid;
    }
}
