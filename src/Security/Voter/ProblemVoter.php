<?php

namespace App\Security\Voter;

use App\Entity\Problem;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProblemVoter extends Voter {

    const VIEW = 'view';
    const EDIT = 'edit';
    const REMOVE = 'remove';
    const STATUS = 'status';
    const ASSIGNEE = 'assignee';
    const MAINTENANCE = 'maintenance';

    private AccessDecisionManagerInterface $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager) {
        $this->decisionManager = $decisionManager;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        $attributes = [
            static::VIEW,
            static::EDIT,
            static::REMOVE,
            static::STATUS,
            static::ASSIGNEE,
            static::MAINTENANCE
        ];

        if(!in_array($attribute, $attributes)) {
            return false;
        }

        if(!$subject instanceof Problem) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool {
        if($attribute !== static::ASSIGNEE && $this->decisionManager->decide($token, [ 'ROLE_ADMIN' ])) {
            return true;
        }

        /** @var User $user */
        $user = $token->getUser();

        switch($attribute) {
            case static::EDIT:
                return $this->canEdit($subject, $user);

            case static::VIEW:
                return $this->canView($subject, $user);

            case static::REMOVE:
                return $this->canRemove($subject, $user);

            case static::STATUS:
                return $this->canChangeStatus($subject, $user);

            case static::ASSIGNEE:
                return $this->canChangeAssignee($subject, $token);

            case static::MAINTENANCE:
                return $this->canSetMaintenance($subject, $token);
        }

        throw new \LogicException('This code should not be reached');
    }

    private function canEdit(Problem $problem, User $user): bool {
        return $problem->getCreatedBy() !== null
            && $problem->getCreatedBy()->getId() === $user->getId();
    }

    private function canView(Problem $problem, User $user): bool {
        return true;
    }

    private function canRemove(Problem $problem, User $user): bool {
        return false;
    }

    private function canChangeStatus(Problem $problem, User $user): bool {
        return false;
    }

    private function canChangeAssignee(Problem $problem, TokenInterface $token): bool {
        if($this->decisionManager->decide($token, [ 'ROLE_ADMIN' ]) !== true) {
            // non AG members are not allowed to change contact person
            return false;
        }

        /** @var User $assignee */
        $assignee = $problem->getAssignee();

        if($assignee === null) {
            // user is allowed to set the contact person
            return true;
        }

        if($assignee !== null && $assignee->getId() === $token->getUser()->getId()) {
            // current contact person is the current user
            return true;
        }

        // otherwise
        return false;
    }

    private function canSetMaintenance(Problem $problem, TokenInterface $token): bool {
        return $this->decisionManager->decide($token, ['ROLE_ADMIN']) === true;
    }
}