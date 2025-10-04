<?php

declare(strict_types=1);

namespace App\Security\User;

use Override;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use LightSaml\Model\Protocol\Response;
use LightSaml\SpBundle\Security\User\UserCreatorInterface;
use Ramsey\Uuid\Uuid;
use SchulIT\CommonBundle\Saml\ClaimTypes;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class UserCreator implements UserCreatorInterface {

    public function __construct(private EntityManagerInterface $em, private UserMapper $userMapper)
    {
    }

    #[Override]
    public function createUser(Response $response): ?UserInterface {
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
