<?php

declare(strict_types=1);

namespace App\Repository;

use Override;
use App\Entity\DeviceType;
use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;

class DeviceTypeRepository implements DeviceTypeRepositoryInterface {

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    #[Override]
    public function findAllByQuery(?string $query, ?Room $room = null): array {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['c', 'd', 'r'])
            ->from(DeviceType::class, 'c')
            ->leftJoin('c.devices', 'd')
            ->leftJoin('d.room', 'r')
            ->orderBy('c.name', 'asc')
            ->addOrderBy('d.name', 'asc');

        if($query !== null && $query !== '' && $query !== '0') {
            $qb->where('d.name LIKE :query')
                ->setParameter('query', '%' . $query . '%');
        }

        if($room instanceof Room) {
            $qb->andWhere('r.id = :room')
                ->setParameter('room', $room->getId());
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function findAll(): array {
        return $this->em->getRepository(DeviceType::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    #[Override]
    public function persist(DeviceType $deviceType): void {
        $this->em->persist($deviceType);
        $this->em->flush();
    }

    #[Override]
    public function remove(DeviceType $deviceType): void {
        $this->em->remove($deviceType);
        $this->em->flush();
    }
}
