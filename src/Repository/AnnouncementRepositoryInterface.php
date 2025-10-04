<?php

declare(strict_types=1);

namespace App\Repository;

use DateTime;
use App\Entity\Announcement;
use App\Entity\Room;

interface AnnouncementRepositoryInterface {

    public function findOneById(int $id): ?Announcement;

    public function countActive(DateTime $today): int;

    /**
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
