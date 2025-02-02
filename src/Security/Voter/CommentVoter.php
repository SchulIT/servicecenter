<?php

namespace App\Security\Voter;

use LogicException;
use App\Entity\Comment;
use App\Entity\Problem;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVoter extends Voter {
    public const string ADD = 'add_comment';
    public const string EDIT = 'edit';
    public const string REMOVE = 'remove';

    public function __construct(private readonly AccessDecisionManagerInterface $decisionManager)
    {
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        $attributes = [
            self::ADD,
            self::EDIT,
            self::REMOVE
        ];

        if($attribute === self::ADD && $subject instanceof Problem) {
            return true;
        }

        return $subject instanceof Comment
            && in_array($attribute, $attributes);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::ADD => $this->canAdd($subject, $token),
            self::EDIT => $this->canEdit($user, $subject),
            self::REMOVE => $this->canRemove($user, $subject),
            default => throw new LogicException('This code should not be reached'),
        };
    }

    private function canEdit(User $user, Comment $comment): bool {
        return $comment->getCreatedBy() !== null
            && $comment->getCreatedBy()->getId() === $user->getId();
    }

    private function canRemove(User $user, Comment $comment): bool {
        return $this->canEdit($user, $comment);
    }

    private function canAdd(Problem $problem, TokenInterface $token): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return $this->decisionManager->decide($token, [ 'ROLE_ADMIN' ]) || $problem->getCreatedBy()->getId() === $user->getId();
    }
}