<?php

namespace App\Exchange;

use App\Repository\UserRepositoryInterface;
use App\Security\User\UserMapper;
use Doctrine\ORM\EntityManagerInterface;
use SchulIT\IdpExchange\Response\AbstractAttribute;
use SchulIT\IdpExchange\Response\UserResponse;
use SchulIT\IdpExchange\Response\ValueAttribute;
use SchulIT\IdpExchange\Response\ValuesAttribute;
use SchulIT\IdpExchangeBundle\Service\UserUpdaterInterface;

class UserUpdater implements UserUpdaterInterface {

    private $userMapper;
    private $userRepository;
    private $em;

    public function __construct(UserMapper $userMapper, UserRepositoryInterface $userRepository, EntityManagerInterface $entityManager) {
        $this->userMapper = $userMapper;
        $this->userRepository = $userRepository;
        $this->em = $entityManager;
    }

    public function startTransaction() {
        $this->em->getConnection()->beginTransaction();
    }

    public function updateUser(UserResponse $response) {
        $user = $this->userRepository->findOneByUsername($response->username);
        /** @var AbstractAttribute[] $attributes */
        $attributes = $response->attributes;

        $attributesArray = [ ];

        foreach($attributes as $attribute) {
            if($attribute instanceof ValueAttribute) {
                $attributesArray[$attribute->name] = $attribute->value;
            } else if($attribute instanceof ValuesAttribute) {
                $attributesArray[$attribute->name] = $attribute->values;
            }
        }

        $this->userMapper->mapUser($user, $attributesArray);

        $this->em->persist($user);
        $this->em->flush();
    }

    public function commit() {
        $this->em->getConnection()->commit();
        $this->em->flush();
    }
}