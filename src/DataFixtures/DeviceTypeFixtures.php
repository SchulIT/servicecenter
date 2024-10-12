<?php

namespace App\DataFixtures;

use App\Entity\DeviceType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DeviceTypeFixtures extends Fixture {

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void {
        $types = [
            'computer' => 'Computer',
            'beamer' => 'Beamer',
            'network' => 'Netzwerk'
        ];

        foreach($types as $alias => $name) {
            $type = (new DeviceType())
                ->setName($name);

            $this->setReference('devicetype.' . $alias, $type);
            $manager->persist($type);
        }

        $manager->flush();
    }
}