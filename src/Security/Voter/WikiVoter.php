<?php

namespace App\Security\Voter;

use App\Entity\WikiAccess;
use App\Entity\WikiArticle;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class WikiVoter extends Voter {

    const VIEW = 'view';
    const ADD = 'add';
    const EDIT = 'edit';
    const REMOVE = 'remove';

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
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool {
        switch ($attribute) {
            case static::VIEW:
                return $this->canView($subject, $token);

            case static::ADD:
            case static::EDIT:
            case static::REMOVE:
                return $this->canAddOrEditOrRemove($subject, $token);
        }

        throw new \LogicException('This code should not be reached');
    }

    private function canView(?WikiArticle $wikiArticle, TokenInterface $token): bool {
        if($wikiArticle === null) {
            // Everyone can see root level
            return true;
        }

        /*
         * Simply walk through the tree of articles/categories
         * and check the permissions.
         */
        while($wikiArticle !== null) {
            if(WikiAccess::Inherit()->equals($wikiArticle->getAccess()) !== true) {
                if($this->decisionManager->decide($token, $this->getRolesForAccess($wikiArticle->getAccess())) !== true) {
                    return false;
                }
            }

            $wikiArticle = $wikiArticle->getParent();
        }

        return true;
    }

    private function canAddOrEditOrRemove(?WikiArticle $wikiArticle, TokenInterface $token): bool {
        if($this->decisionManager->decide($token, ['ROLE_ADMIN']) !== true) {
            // user must have at least ROLE_ADMIN
            return false;
        }

        // user must have permission to view the article
        return $this->canView($wikiArticle, $token);
    }

    private function getRolesForAccess(WikiAccess $access): array {
        if(WikiAccess::All()->equals($access)) {
            return ['ROLE_USER'];
        } if(WikiAccess::Admin()->equals($access)) {
            return ['ROLE_ADMIN'];
        } else if(WikiAccess::SuperAdmin()->equals($access)) {
            return ['ROLE_SUPER_ADMIN'];
        }

        return [ ];
    }
}