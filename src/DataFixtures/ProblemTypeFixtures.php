<?php

namespace App\DataFixtures;

use App\Entity\DeviceType;
use App\Entity\ProblemType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProblemTypeFixtures extends Fixture implements DependentFixtureInterface {

    /**
     * @inheritDoc
     */
    public function getDependencies() {
        return [
            DeviceTypeFixtures::class
        ];
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $this->loadComputerProblemTypes($manager);

        $manager->flush();
    }

    private function loadComputerProblemTypes(ObjectManager $manager) {
        $types = [
            'signon' => 'Anmeldung',
            'applications' => 'Anwendungen',
            'updates' => 'Updates'
        ];

        /** @var DeviceType $deviceType */
        $deviceType = $this->getReference('devicetype.computer');

        foreach($types as $type) {
            $problemType = (new ProblemType())
                ->setName($type)
                ->setDeviceType($deviceType);

            $manager->persist($problemType);
        }
    }
}