<?php

namespace App\Security\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpResponseToken;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SchoolIT\CommonBundle\Security\AuthenticationEvent;
use SchoolIT\CommonBundle\Security\SecurityEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserUpdater implements EventSubscriberInterface  {
    /** @var UserMapper  */
    private $userMapper;

    /** @var EntityManagerInterface  */
    private $em;

    /** @var LoggerInterface|NullLogger  */
    private $logger;

    public function __construct(EntityManagerInterface $em, UserMapper $userMapper, LoggerInterface $logger = null) {
        $this->userMapper = $userMapper;
        $this->em = $em;
        $this->logger = $logger ?? new NullLogger();
    }

    public function onAuthenticationSuccess(AuthenticationEvent $event) {
        $token = $event->getToken();
        $user = $event->getUser();

        if($token === null) {
            $this->logger
                ->debug('Token is null, cannot update user');
            return;
        }

        if($user === null) {
            $this->logger
                ->debug('User is null, cannot update user');
            return;
        }

        if(!$user instanceof User) {
            $this->logger
                ->debug(sprintf('User is not of type "%s" ("%s" given), cannot update user', User::class, get_class($user)));
        }

        $response = $token->getResponse();

        $user = $this->userMapper->mapUser($user, $response);
        $this->em->persist($user);
        $this->em->flush();

        $this->logger
            ->debug('User updated from SAML response');
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            SecurityEvents::SAML_AUTHENTICATION_SUCCESS => [
                [ 'onAuthenticationSuccess', 10 ]
            ]
        ];
    }
}