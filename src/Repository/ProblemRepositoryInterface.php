<?php

namespace App\Repository;

use App\Entity\Device;
use App\Entity\Problem;
use App\Entity\ProblemFilter;
use App\Entity\Room;
use App\Entity\User;

interface ProblemRepositoryInterface {

    /**
     * @param int $id
     * @return Problem|null
     */
    public function findOneById($id);

    /**
     * @param int[] $ids
     * @return Problem[]
     */
    public function findByIds(array $ids);

    /**
     * @param User $user
     * @param null $sortColumn
     * @param string $order
     * @return Problem[]
     */
    public function findByUser(User $user, $sortColumn = null, $order = 'asc');

    /**
     * Gets all problems a user named as contact person
     *
     * @param User $user
     * @param string $sortColumn
     * @param string $order
     * @return Problem[]
     */
    public function findByContactPerson(User $user, $sortColumn = null, $order = 'asc');

    /**
     * @param User $user
     * @return int
     */
    public function countByUser(User $user);

    /**
     * @return int
     */
    public function countOpen();

    /**
     * @param Room $room
     * @return Problem[]
     */
    public function findOpenByRoom(Room $room);

    /**
     * @param Device $device
     * @return Problem[]
     */
    public function findOpenByDevice(Device $device);

    /**
     * @return Problem[]
     */
    public function findOpen();

    /**
     * @param ProblemFilter $filter
     * @param int $page
     * @param string|null $query
     * @return Problem[]
     */
    public function getProblems(ProblemFilter $filter, $page = 1, $query = null);

    /**
     * @param ProblemFilter $filter
     * @param string|null $query
     * @return int
     */
    public function countProblems(ProblemFilter $filter, $query = null);

    public function persist(Problem $problem);

    public function remove(Problem $problem);
}