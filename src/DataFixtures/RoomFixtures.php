<?php

namespace App\DataFixtures;

use App\Entity\Room;
use App\Entity\RoomCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class RoomFixtures extends Fixture {

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $category = (new RoomCategory())
            ->setName('Computerräume');

        $roomA = (new Room())
            ->setName('PC01')
            ->setAlias('pc01')
            ->setCategory($category);

        $roomB = (new Room())
            ->setName('PC02')
            ->setAlias('pc02')
            ->setCategory($category);

        $manager->persist($category);
        $manager->persist($roomA);
        $manager->persist($roomB);

        $this->setReference('room', $roomA);

        $manager->flush();
    }
}