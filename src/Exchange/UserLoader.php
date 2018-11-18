<?php

namespace App\Exchange;

use App\Repository\UserRepositoryInterface;
use SchoolIT\IdpExchangeBundle\Service\UserLoaderInterface;

class UserLoader implements UserLoaderInterface {

    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function getUsers($limit = null, $offset = null) {
        return $this->userRepository->findAll($limit, $offset);
    }
}