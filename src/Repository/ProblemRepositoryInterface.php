<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Device;
use App\Entity\Problem;
use App\Entity\ProblemFilter;
use App\Entity\Room;
use App\Entity\User;

interface ProblemRepositoryInterface {

    public function findOneById(int $id): ?Problem;

    /**
     * @param int[] $ids
     * @return Problem[]
     */
    public function findByIds(array $ids): array;

    /**
     * @param string[] $uuids
     * @return Problem[]
     */
    public function findByUuids(array $uuids): array;

    /**
     * @return Problem[]
     */
    public function findByUser(User $user, ?string $sortColumn = null, ?string $order = 'asc'): array;

    /**
     * Gets all problems a user named as contact person
     * @return Problem[]
     */
    public function findByAssignee(User $user, string|null $sortColumn = null, string $order = 'asc'): array;

    /**
     * @param int $number Amount of problems to fetch
     * @param bool $includeMaintenance Whether to include maintenance problems
     * @return Problem[]
     */
    public function getLatest(int $number, bool $includeMaintenance): array;

    public function countByUser(User $user): int;

    public function countOpen(): int;

    /**
     * @return Problem[]
     */
    public function findOpenByRoom(Room $room): array;

    /**
     * @return Problem[]
     */
    public function findOpenByDevice(Device $device): array;

    /**
     * @param int[] $deviceIds
     * @return Problem[]
     */
    public function findOpenByDeviceIds(array $deviceIds, ?int $type): array;

    /**
     * @param Problem $problem
     * @param int $count
     * @return Problem[]
     */
    public function findRelated(Problem $problem, int $count = 5): array;

    /**
     * @return Problem[]
     */
    public function findOpen(): array;

    /**
     * @return Problem[]
     */
    public function getProblems(ProblemFilter $filter, int $page = 1, string|null $query = null): array;

    public function countProblems(ProblemFilter $filter, string|null $query = null): int;

    public function persist(Problem $problem);

    public function remove(Problem $problem);
}
