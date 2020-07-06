<?php

namespace App\Security\User;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use LightSaml\Model\Protocol\Response;
use LightSaml\SpBundle\Security\User\UserCreatorInterface;
use LightSaml\SpBundle\Security\User\UsernameMapperInterface;
use Ramsey\Uuid\Uuid;
use SchulIT\CommonBundle\Saml\ClaimTypes;
use Symfony\Component\Security\Core\User\UserInterface;

class UserCreator implements UserCreatorInterface {
    /** @var ObjectManager */
    private $em;

    /** @var UserMapper */
    private $userMapper;

    /** @var UsernameMapperInterface  */
    private $usernameMapper;
    
    public function __construct(EntityManagerInterface $em, UserMapper $userMapper, UsernameMapperInterface $usernameMapper) {
        $this->em = $em;
        $this->userMapper = $userMapper;
        $this->usernameMapper = $usernameMapper;
    }

    /**
     * @param Response $response
     * @return UserInterface|null
     */
    public function createUser(Response $response) {
        // Second chance: map user by ID
        $id = $response->getFirstAssertion()
            ->getFirstAttributeStatement()
            ->getFirstAttributeByName(ClaimTypes::ID)
            ->getFirstAttributeValue();

        $user = (new User())
            ->setIdpId(Uuid::fromString($id));

        $this->userMapper->mapUser($user, $response);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }


}