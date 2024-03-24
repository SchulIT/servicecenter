<?php

namespace App\Repository;

use DateTime;
use App\Entity\Announcement;
use App\Entity\Room;

interface AnnouncementRepositoryInterface {

    /**
     * @param int $id
     * @return Announcement|null
     */
    public function findOneById($id);

    /**
     * @param DateTime $today
     * @return int
     */
    public function countActive(DateTime $today);

    /**
     * @param DateTime $today
     * @return Announcement[]
     */
    public function findActive(DateTime $today);

    /**
     * @param Room $room
     * @param DateTime $today
     * @return Announcement[]
     */
    public function findActiveByRoom(Room $room, DateTime $today);

    public function persist(Announcement $announcement);

    public function remove(Announcement $announcement);
}