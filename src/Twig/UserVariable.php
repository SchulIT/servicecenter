<?php

namespace App\Twig;

use App\Entity\User;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserVariable {
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return SamlSpToken
     * @throws \Exception
     */
    private function getToken() {
        $token = $this->tokenStorage->getToken();

        if(!$token instanceof SamlSpToken) {
            throw new \Exception(sprintf('Token must be of type "%s" ("%s" given)', SamlSpToken::class, get_class($token)));
        }

        return $token;
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->getToken()->getUser();
    }

    public function getStudentId() {
        return $this->getToken()->getAttribute('student_id');
    }

    public function getFirstname() {
        return $this->getUser()->getFirstname();
    }

    public function getLastname() {
        return $this->getUser()->getLastname();
    }

    public function getEmailAddress() {
        return $this->getUser()->getEmail();
    }

    public function getServices() {
        return $this->getToken()->getAttribute('services');
    }
}