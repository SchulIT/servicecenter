<?php

declare(strict_types=1);

namespace App\Security\Voter;

use Override;
use LogicException;
use App\Entity\WikiAccess;
use App\Entity\WikiArticle;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class WikiVoter extends Voter {

    public const string VIEW = 'view';
    public const string ADD = 'add';
    public const string EDIT = 'edit';
    public const string REMOVE = 'remove';

    public function __construct(private readonly AccessDecisionManagerInterface $decisionManager)
    {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function supports($attribute, $subject): bool {
        $attributes = [
            static::VIEW,
            static::ADD,
            static::EDIT,
            static::REMOVE
        ];

        if(!in_array($attribute, $attributes)) {
            return false;
        }

        return $subject instanceof WikiArticle || $subject === null;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            static::VIEW => $this->canView($subject, $token),
            static::ADD, static::EDIT, static::REMOVE => $this->canAddOrEditOrRemove($subject, $token),
            default => throw new LogicException('This code should not be reached'),
        };
    }

    private function canView(?WikiArticle $wikiArticle, TokenInterface $token): bool {
        if(!$wikiArticle instanceof WikiArticle) {
            // Everyone can see root level
            return true;
        }

        /*
         * Simply walk through the tree of articles/categories
         * and check the permissions.
         */
        while($wikiArticle instanceof WikiArticle) {
            if(WikiAccess::Inherit === $wikiArticle->getAccess() && !$this->decisionManager->decide($token, $this->getRolesForAccess($wikiArticle->getAccess()))) {
                return false;
            }

            $wikiArticle = $wikiArticle->getParent();
        }

        return true;
    }

    private function canAddOrEditOrRemove(?WikiArticle $wikiArticle, TokenInterface $token): bool {
        if(!$this->decisionManager->decide($token, ['ROLE_ADMIN'])) {
            // user must have at least ROLE_ADMIN
            return false;
        }

        // user must have permission to view the article
        return $this->canView($wikiArticle, $token);
    }

    private function getRolesForAccess(WikiAccess $access): array {
        if(WikiAccess::All === $access) {
            return ['ROLE_USER'];
        } if (WikiAccess::Admin === $access) {
            return ['ROLE_ADMIN'];
        } elseif (WikiAccess::SuperAdmin === $access) {
            return ['ROLE_SUPER_ADMIN'];
        }

        return [ ];
    }
}
