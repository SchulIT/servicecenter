<?php

declare(strict_types=1);

namespace App\Security\Voter;

use Override;
use LogicException;
use App\Entity\Problem;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProblemVoter extends Voter {

    public const string VIEW = 'view';
    public const string EDIT = 'edit';
    public const string REMOVE = 'remove';
    public const string STATUS = 'status';
    public const string ASSIGNEE = 'assignee';
    public const string MAINTENANCE = 'maintenance';

    public function __construct(private readonly AccessDecisionManagerInterface $decisionManager)
    {
    }

    /**
     * @inheritDoc
     */
    #[Override]
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
        return $subject instanceof Problem;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool {
        if($attribute !== self::ASSIGNEE && $this->decisionManager->decide($token, [ 'ROLE_ADMIN' ])) {
            return true;
        }

        /** @var User $user */
        $user = $token->getUser();
        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            self::VIEW => $this->canView(),
            self::REMOVE => $this->canRemove(),
            self::STATUS => $this->canChangeStatus(),
            self::ASSIGNEE => $this->canChangeAssignee($subject, $token),
            self::MAINTENANCE => $this->canSetMaintenance($token),
            default => throw new LogicException('This code should not be reached'),
        };
    }

    private function canEdit(Problem $problem, User $user): bool {
        return $problem->getCreatedBy() instanceof User
            && $problem->getCreatedBy()->getId() === $user->getId();
    }

    private function canView(): bool
    {
        return true;
    }

    private function canRemove(): bool
    {
        return false;
    }

    private function canChangeStatus(): bool
    {
        return false;
    }

    private function canChangeAssignee(Problem $problem, TokenInterface $token): bool {
        if(!$this->decisionManager->decide($token, [ 'ROLE_ADMIN' ])) {
            // non AG members are not allowed to change contact person
            return false;
        }

        $assignee = $problem->getAssignee();

        if(!$assignee instanceof User) {
            // user is allowed to set the contact person
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }
        // otherwise
        // current contact person is the current user
        return $assignee->getId() === $user->getId();
    }

    private function canSetMaintenance(TokenInterface $token): bool {
        return $this->decisionManager->decide($token, ['ROLE_ADMIN']);
    }
}
