<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\User;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as SymfonyWebTestCase;
use Symfony\Component\BrowserKit\Cookie;

abstract class WebTestCase extends SymfonyWebTestCase {
    protected function logIn(Client $client, User $user) {
        $session = $client->getContainer()->get('session');

        $firewallContext = 'secured';

        $attributes = [
            'name_id' => 'admin',
            'student_id' => null,
            'services' => [ ]
        ];

        // you may need to use a different token class depending on your application.
        // for example, when using Guard authentication you must instantiate PostAuthenticationGuardToken
        $token = new SamlSpToken(['ROLE_ADMIN'], null, $attributes, $user);
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }
}
