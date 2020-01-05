<?php

namespace App\Helper\Statistics;

use App\Entity\Problem;
use App\Entity\ProblemType;
use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;

class StatisticsHelper {
    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @param Statistics $statistics
     * @return StatisticsResult[]
     */
    public function getStatistics(Statistics $statistics) {
        $problems = $this->getProblems($statistics);

        if($statistics->getMode() === Statistics::MODE_TYPES) {
            return $this->getStatisticsForTypes($problems);
        } else if($statistics->getMode() === Statistics::MODE_ROOMS) {
            return $this->getStatisticsForRooms($problems);
        }

        throw new \LogicException(sprintf('Invalid statistics mode "%s"', $statistics->getMode()));
    }

    /**
     * @param Problem[] $problems
     * @return StatisticsResult[]
     */
    private function getStatisticsForRooms(array $problems) {
        $rooms = [ ];

        foreach($problems as $problem) {
            $roomId = $problem->getDevice()->getRoom()->getId();

            if(!isset($rooms[$roomId])) {
                $rooms[$roomId] = [
                    'name' => $problem->getDevice()->getRoom()->getName(),
                    'count' => 0
                ];
            }

            $rooms[$roomId]['count']++;
        }

        $count = count($problems);
        $result = [ ];

        foreach($rooms as $room) {
            $percentace = (float)$room['count'] / $count * 100;
            $result[] = new StatisticsResult($room['name'], $room['count'], $percentace);
        }

        return $result;
    }

    /**
     * @param Problem[] $problems
     * @return StatisticsResult[]
     */
    private function getStatisticsForTypes(array $problems) {
        $types = [ ];

        foreach($problems as $problem) {
            $typeId = $problem->getProblemType()->getId();

            if(!isset($types[$typeId])) {
                $types[$typeId] = [
                    'name' => $problem->getProblemType()->getName(),
                    'count' => 0
                ];
            }

            $types[$typeId]['count']++;
        }

        $count = count($problems);
        $result = [ ];

        foreach($types as $type) {
            $percentace = (float)$type['count'] / $count * 100;
            $result[] = new StatisticsResult($type['name'], $type['count'], $percentace);
        }

        return $result;
    }

    /**
     * @param Statistics $statistics
     * @return Problem[]
     */
    private function getProblems(Statistics $statistics) {
        $qb = $this->em->createQueryBuilder();
        $qbInner = $this->em->createQueryBuilder();

        $typeIds = array_map(function(ProblemType $type) {
            return $type->getId();
        }, $statistics->getTypes()->toArray());

        $roomIds = array_map(function(Room $room) {
            return $room->getId();
        }, $statistics->getRooms()->toArray());

        $qbInner->select('pInner.id')
            ->from(Problem::class, 'pInner')
            ->leftJoin('pInner.device', 'dInner')
            ->leftJoin('dInner.room', 'rInner')
            ->leftJoin('pInner.problemType', 'tInner')
            ->where(
                $qbInner->expr()->in('tInner.id', ':types')
            )
            ->andWhere(
                $qbInner->expr()->in('rInner.id', ':rooms')
            );

        if($statistics->isIncludeSolved() !== true) {
            $qbInner->andWhere('pInner.isOpen = false');
        }

        if($statistics->isIncludeMaintenance() !== true) {
            $qbInner->andWhere('pInner.isMaintenance = false');
        }

        $qbInner->andWhere('p.createdAt >= :start')
            ->andWhere('p.createdAt <= :end');

        $qb->setParameter('types', $typeIds)
            ->setParameter('rooms', $roomIds)
            ->setParameter('start', $statistics->getStart())
            ->setParameter('end', $statistics->getEnd());

        $qb->select(['p', 'd', 'r', 't'])
            ->from(Problem::class, 'p')
            ->leftJoin('p.device', 'd')
            ->leftJoin('d.room', 'r')
            ->leftJoin('p.problemType', 't')
            ->where(
                $qb->expr()->in('p.id', $qbInner->getDQL())
            );

        return $qb->getQuery()->getResult();
    }
}