<?php

namespace App\Repository;

use App\Entity\Placard;
use App\Entity\PlacardDevice;
use App\Entity\Room;

interface PlacardRepositoryInterface {

    /**
     * @return Placard[]
     */
    public function findAll();

    /**
     * @param Room $room
     * @return Placard
     */
    public function findOneByRoom(Room $room);

    /**
     * @param Placard $placard
     * @param PlacardDevice[] $oldDevices Devices which are removed from the placard
     * @return mixed
     */
    public function persist(Placard $placard, array $oldDevices = [ ]);

    public function remove(Placard $placard);
}