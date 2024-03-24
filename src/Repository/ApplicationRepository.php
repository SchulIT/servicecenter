<?php

namespace App\Repository;

use App\Entity\Application;
use Doctrine\ORM\EntityManagerInterface;

class ApplicationRepository implements ApplicationRepositoryInterface {
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function findAll(): array {
        return $this->em->getRepository(Application::class)
            ->findAll();
    }

    public function findOneByApiKey($key): ?Application {
        return $this->em->getRepository(Application::class)
            ->findOneBy(['apiKey' => $key]);
    }

    public function persist(Application $application): void {
        $this->em->persist($application);
        $this->em->flush();
    }

    public function remove(Application $application): void {
        $this->em->remove($application);
        $this->em->flush();
    }
}