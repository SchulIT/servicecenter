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
    public function findOneById(int $id): ?Announcement;

    /**
     * @param DateTime $today
     * @return int
     */
    public function countActive(DateTime $today): int;

    /**
     * @param DateTime $today
     * @return Announcement[]
     */
    public function findActive(DateTime $today): array;

    /**
     * @return Announcement[]
     */
    public function findActiveByRoom(Room $room, DateTime $today): array;

    public function persist(Announcement $announcement): void;

    public function remove(Announcement $announcement): void;
}