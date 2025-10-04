<?php

declare(strict_types=1);

namespace App\Tests\Functional\Placards;

use App\Entity\Placard;
use App\Entity\PlacardDevice;
use App\Entity\Room;
use App\Entity\RoomCategory;
use App\Entity\User;
use App\Tests\Functional\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Client;

final class EditPlacardTest extends WebTestCase {

    private \Doctrine\ORM\EntityManagerInterface $em;

    /** @var Client */
    private \Symfony\Bundle\FrameworkBundle\KernelBrowser $client;

    private \App\Entity\Room $room;

    private ?\App\Entity\User $user = null;

    public function setUp(): void {
        $this->client = self::createClient();

        $this->em = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();

        $category = (new RoomCategory())
            ->setName('Testcategory');

        $this->em->persist($category);
        $this->em->flush();

        $this->room = (new Room())
            ->setName('Testroom')
            ->setAlias('testroom')
            ->setCategory($category);

        $this->user = (new User())
            ->setUsername('admin')
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail('admin@test.school.it')
            ->setLastname('Administrator')
            ->setFirstname('Test');

        $this->em->persist($this->user);

        $this->em->persist($this->room);
        $this->em->flush();
    }

    #[\Override]
    public function tearDown(): void {
        $this->em->close();
        $this->em = $this->user = $this->room = null;

        parent::tearDown();
    }

    public function testEdit(): void {
        $placard = (new Placard())
            ->setRoom($this->room)
            ->setHeader('Header');

        $device1 = (new PlacardDevice())
            ->setAv('AV 1')
            ->setBeamer('Beamer 1')
            ->setSource('Source 1')
            ->setPlacard($placard);

        $device2 = (new PlacardDevice())
            ->setAv('AV 2')
            ->setBeamer('Beamer 2')
            ->setSource('Source 2')
            ->setPlacard($placard);

        $placard->addDevice($device1);
        $placard->addDevice($device2);

        $this->em->persist($placard);
        $this->em->persist($device1);
        $this->em->persist($device2);
        $this->em->flush();

        $this->client->restart();
        $this->client->followRedirects();

        $this->logIn($this->client, $this->user);

        $crawler = $this->client->request('GET', '/placards/testroom/edit');
        $button = $crawler->filter('button[type=submit]')->first();
        $form = $button->form();

        $values = $form->getPhpValues();

        $values['placard']['devices'][0]['source'] = 'New-Source 1';
        $values['placard']['devices'][0]['beamer'] = 'New-Beamer 1';
        $values['placard']['devices'][0]['av'] = 'New-AV 1';

        unset($values['placard']['devices'][1]);

        $values['placard']['devices'][2]['source'] = 'New-Source 3';
        $values['placard']['devices'][2]['beamer'] = 'New-Beamer 3';
        $values['placard']['devices'][2]['av'] = 'New-AV 3';

        $this->client->request($form->getMethod(), $form->getUri(), $values);

        $placard = $this->em->getRepository(Placard::class)
            ->findOneBy(['room' => $this->room]);

        $this->assertNotNull($placard);
        $this->assertEquals(2, $placard->getDevices()->count());

        /** @var PlacardDevice $first */
        $first = $placard->getDevices()->get(0);
        $this->assertEquals('New-Source 1', $first->getSource());
        $this->assertEquals('New-Beamer 1', $first->getBeamer());
        $this->assertEquals('New-AV 1', $first->getAv());

        /** @var PlacardDevice $second */
        $second = $placard->getDevices()->get(1);
        $this->assertEquals('New-Source 3', $second->getSource());
        $this->assertEquals('New-Beamer 3', $second->getBeamer());
        $this->assertEquals('New-AV 3', $second->getAv());
    }

}
