<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\Problem;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVoter extends Voter {
    const ADD = 'add_comment';
    const EDIT = 'edit';
    const REMOVE = 'remove';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager) {
        $this->decisionManager = $decisionManager;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        $attributes = [
            static::ADD,
            static::EDIT,
            static::REMOVE
        ];

        if($attribute === static::ADD && $subject instanceof Problem) {
            return true;
        }

        return $subject instanceof Comment
            && in_array($attribute, $attributes);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        switch($attribute) {
            case static::ADD:
                return $this->canAdd($subject, $token);

            case static::EDIT:
                return $this->canEdit($token->getUser(), $subject);

            case static::REMOVE:
                return $this->canRemove($token->getUser(), $subject);
        }

        throw new \LogicException('This code should not be reached');
    }

    private function canEdit(User $user, Comment $comment) {
        return $comment->getCreatedBy() !== null
            && $comment->getCreatedBy()->getId() === $user->getId();
    }

    private function canRemove(User $user, Comment $comment) {
        return $this->canEdit($user, $comment);
    }

    private function canAdd(Problem $problem, TokenInterface $token) {
        return $this->decisionManager->decide($token, [ 'ROLE_ADMIN' ]) || $problem->getCreatedBy()->getId() === $token->getUser()->getId();
    }
}