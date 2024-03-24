<?php

namespace App\Security\Voter;

use LogicException;
use App\Entity\Problem;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProblemVoter extends Voter {

    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const REMOVE = 'remove';
    public const STATUS = 'status';
    public const ASSIGNEE = 'assignee';
    public const MAINTENANCE = 'maintenance';

    public function __construct(private readonly AccessDecisionManagerInterface $decisionManager)
    {
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        $attributes = [
            self::VIEW,
            self::EDIT,
            self::REMOVE,
            self::STATUS,
            self::ASSIGNEE,
            self::MAINTENANCE
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
        if($attribute !== self::ASSIGNEE && $this->decisionManager->decide($token, [ 'ROLE_ADMIN' ])) {
            return true;
        }

        /** @var User $user */
        $user = $token->getUser();
        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            self::VIEW => $this->canView($subject, $user),
            self::REMOVE => $this->canRemove($subject, $user),
            self::STATUS => $this->canChangeStatus($subject, $user),
            self::ASSIGNEE => $this->canChangeAssignee($subject, $token),
            self::MAINTENANCE => $this->canSetMaintenance($subject, $token),
            default => throw new LogicException('This code should not be reached'),
        };
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

        $assignee = $problem->getAssignee();

        if($assignee === null) {
            // user is allowed to set the contact person
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if($assignee->getId() === $user->getId()) {
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