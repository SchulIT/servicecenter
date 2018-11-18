<?php

namespace App\Helper\Problems;

use App\Entity\Problem;
use App\Security\Voter\ProblemVoter;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SetContactPersonAction extends AbstractBulkAction {

    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker) {
        parent::__construct($authorizationChecker);

        $this->tokenStorage = $tokenStorage;
    }

    protected function getAttributes() {
        return ProblemVoter::CONTACTPERSON;
    }

    protected function perform(Problem $problem): bool {
        $user = $this->tokenStorage->getToken()
            ->getUser();

        if($user !== null && $problem->getContactPerson() === null) {
            $problem->setContactPerson($user);
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string {
        return 'contact_person';
    }
}