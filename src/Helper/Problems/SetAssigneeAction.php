<?php

declare(strict_types=1);

namespace App\Helper\Problems;

use Override;
use App\Entity\Problem;
use App\Entity\User;
use App\Security\Voter\ProblemVoter;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SetAssigneeAction extends AbstractBulkAction {

    public function __construct(private readonly TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker) {
        parent::__construct($authorizationChecker);
    }

    #[Override]
    protected function getAttributes(): string {
        return ProblemVoter::ASSIGNEE;
    }

    #[Override]
    protected function perform(Problem $problem): bool {
        $user = $this->tokenStorage->getToken()
            ->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if(!$problem->getAssignee() instanceof User) {
            $problem->setAssignee($user);
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getName(): string {
        return 'assignee';
    }
}
