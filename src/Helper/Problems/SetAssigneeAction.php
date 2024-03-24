<?php

namespace App\Helper\Problems;

use App\Entity\Problem;
use App\Entity\User;
use App\Security\Voter\ProblemVoter;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SetAssigneeAction extends AbstractBulkAction {

    public function __construct(private readonly TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker) {
        parent::__construct($authorizationChecker);
    }

    protected function getAttributes(): string {
        return ProblemVoter::ASSIGNEE;
    }

    protected function perform(Problem $problem): bool {
        $user = $this->tokenStorage->getToken()
            ->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if($problem->getAssignee() === null) {
            $problem->setAssignee($user);
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string {
        return 'assignee';
    }
}