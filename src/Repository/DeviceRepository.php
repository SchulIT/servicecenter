<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Device;
use App\Entity\DeviceType;
use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Override;

class DeviceRepository implements DeviceRepositoryInterface {
    use ReturnTrait;

    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Override]
    public function findOneById(int $id): ?Device {
        return $this->em->getRepository(Device::class)
            ->findOneBy(['id' => $id ]);
    }

    #[Override]
    public function persist(Device $device): void {
        $this->em->persist($device);
        $this->em->flush();
    }

    #[Override]
    public function remove(Device $device): void {
        $this->em->remove($device);
        $this->em->flush();
    }

    #[Override]
    public function findAllPaginated(PaginationQuery $paginationQuery, ?Room $room = null, ?DeviceType $deviceType = null, ?string $query = null): PaginatedResult {
        $qb = $this->em->createQueryBuilder()
            ->select(['d', 'r', 't'])
            ->from(Device::class, 'd')
            ->join('d.room', 'r')
            ->join('d.type', 't')
            ->orderBy('d.name', 'asc')
            ->addOrderBy('r.name', 'asc');

        if($room instanceof Room) {
            $qb->andWhere('r.id = :room')->setParameter('room', $room);
        }

        if($deviceType instanceof DeviceType) {
            $qb->andWhere('t.id = :type')->setParameter('type', $deviceType);
        }

        if(!empty($query)) {
            $qb->andWhere('d.name LIKE :query')->setParameter('query', '%' . $query . '%');
        }

        return PaginatedResult::fromQueryBuilder($qb, $paginationQuery);
    }
}
