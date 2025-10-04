<?php

declare(strict_types=1);

namespace App\Tests\Functional\Placards;

use App\Entity\Placard;
use App\Entity\Room;
use App\Entity\RoomCategory;
use App\Entity\User;
use App\Tests\Functional\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Client;

final class AddPlacardTest extends WebTestCase {

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

    public function testAddWithoutDevices(): void {
        $this->client->restart();
        $this->client->followRedirects();

        $this->logIn($this->client, $this->user);

        $crawler = $this->client->request('GET', '/placards/add');
        $button = $crawler->filter('button[type=submit]')->first();
        $form = $button->form();

        $form['placard[group_general][room]']->setValue($this->room->getId());
        $form['placard[group_general][header]']->setValue('Header');

        $this->client->submit($form);

        $placard = $this->em->getRepository(Placard::class)
            ->findOneBy(['room' => $this->room]);

        $this->assertNotNull($placard);
        $this->assertEquals('Header', $placard->getHeader());
        $this->assertEquals(0, $placard->getDevices()->count());
    }

    public function testIfAddingExistingPlacardIsImpossible(): void {
        $this->client->restart();
        $this->client->followRedirects();

        $this->logIn($this->client, $this->user);

        $placard = (new Placard())
            ->setRoom($this->room)
            ->setHeader('Header');

        $this->em->persist($placard);
        $this->em->flush();

        $crawler = $this->client->request('GET', '/placards/add');
        $button = $crawler->filter('button[type=submit]')->first();
        $form = $button->form();

        $values = $form->getPhpValues();
        $values['placard']['group_general']['room'] = $this->room->getId();
        $values['placard']['group_general']['header'] = 'New Header';

        $crawler = $this->client->request($form->getMethod(), $form->getUri(), $values);

        $this->assertEquals($form->getUri(), $crawler->getUri(), 'Test if creating an existing placard does not create it again.');
        $select = $crawler->filter('#placard_group_general_room')->first();
        $this->assertEquals('form-control is-invalid', $select->attr('class'), 'Test if creating an existing placard adds an "is-valid" class to the select box');
    }

    public function testAddWithDevices(): void {
        $this->client->restart();
        $this->client->followRedirects();

        $this->logIn($this->client, $this->user);

        $crawler = $this->client->request('GET', '/placards/add');
        $button = $crawler->filter('button[type=submit]')->first();
        $form = $button->form();

        $values = $form->getPhpValues();
        $values['placard']['group_general']['room'] = $this->room->getId();
        $values['placard']['group_general']['header'] = 'Header';

        $values['placard']['devices'][0]['source'] = 'Source 1';
        $values['placard']['devices'][0]['beamer'] = 'Beamer 1';
        $values['placard']['devices'][0]['av'] = 'AV 1';

        $values['placard']['devices'][1]['source'] = 'Source 2';
        $values['placard']['devices'][1]['beamer'] = 'Beamer 2';
        $values['placard']['devices'][1]['av'] = 'AV 2';

        $this->client->request($form->getMethod(), $form->getUri(), $values);

        $placard = $this->em->getRepository(Placard::class)
            ->findOneBy(['room' => $this->room]);

        $this->assertNotNull($placard);
        $this->assertEquals('Header', $placard->getHeader());
        $this->assertEquals(2, $placard->getDevices()->count());

        $first = $placard->getDevices()->get(0);
        $this->assertEquals('Source 1', $first->getSource());
        $this->assertEquals('Beamer 1', $first->getBeamer());
        $this->assertEquals('AV 1', $first->getAV());

        $second = $placard->getDevices()->get(1);
        $this->assertEquals('Source 2', $second->getSource());
        $this->assertEquals('Beamer 2', $second->getBeamer());
        $this->assertEquals('AV 2', $second->getAV());
    }
}
