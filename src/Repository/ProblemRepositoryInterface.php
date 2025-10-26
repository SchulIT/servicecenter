<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Device;
use App\Entity\Problem;
use App\Entity\ProblemType;
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
     * @param PaginationQuery $paginationQuery
     * @param Problem $problem
     * @return PaginatedResult<Problem>
     */
    public function findRelatedPaginated(PaginationQuery $paginationQuery, Problem $problem): PaginatedResult;

    /**
     * @param PaginationQuery $paginationQuery
     * @param Room|null $room
     * @param ProblemType|null $problemType
     * @param string|null $query
     * @param bool $onlyOpen
     * @return PaginatedResult<Problem>
     */
    public function findAllPaginated(PaginationQuery $paginationQuery, Room|null $room = null, ProblemType|null $problemType = null, string|null $query = null, bool $onlyOpen = true): PaginatedResult;

    /**
     * @return Problem[]
     */
    public function findOpen(): array;

    public function persist(Problem $problem);

    public function remove(Problem $problem);
}
