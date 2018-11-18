<?php

namespace App\Security\Voter;

use App\Entity\WikiAccessInterface;
use App\Entity\WikiArticle;
use App\Entity\WikiCategory;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class WikiVoter extends Voter {

    const VIEW = 'view';
    const ADD = 'add';
    const EDIT = 'edit';
    const DELETE = 'delete';

    /**
     * Maps wiki access levels to roles
     *
     * @var array
     */
    private static $accessMap = [
        WikiAccessInterface::ACCESS_ALL => ['ROLE_USER'],
        WikiAccessInterface::ACCESS_ADMIN => ['ROLE_ADMIN_USER'],
        WikiAccessInterface::ACCESS_AG => ['ROLE_AG_USER']
    ];

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager) {
        $this->decisionManager = $decisionManager;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        $attributes = [
            static::VIEW,
            static::ADD,
            static::EDIT,
            static::DELETE
        ];

        if(!in_array($attribute, $attributes)) {
            return false;
        }

        return $subject instanceof WikiAccessInterface || $subject === null;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        switch ($attribute) {
            case static::VIEW:
                return $this->canView($subject, $token);

            case static::ADD:
            case static::EDIT:
            case static::DELETE:
                return $this->canAddOrEditOrDelete($subject, $token);
        }

        throw new \LogicException('This code should not be reached');
    }

    private function canView(?WikiAccessInterface $wikiAccess, TokenInterface $token) {
        if($wikiAccess === null) {
            // Everyone can see root level
            return true;
        }

        /*
         * Simply walk through the tree of articles/categories
         * and check the permissions.
         */

        while($wikiAccess !== null) {
            if(in_array($wikiAccess->getAccess(), static::$accessMap)) {
                if($this->decisionManager->decide($token, static::$accessMap[$wikiAccess->getAccess()]) !== true) {
                    return false;
                }
            }

            if($wikiAccess instanceof WikiArticle) {
                $wikiAccess = $wikiAccess->getCategory();
            } else if($wikiAccess instanceof WikiCategory) {
                $wikiAccess = $wikiAccess->getParent();
            } else {
                throw new \LogicException(sprintf('You must specify logic for retrieving a parent for class %s', get_class($wikiAccess)));
            }
        }

        return true;
    }

    private function canAddOrEditOrDelete(?WikiAccessInterface $wikiAccess, TokenInterface $token) {
        if($this->decisionManager->decide($token, ['ROLE_AG_USER']) !== true) {
            // user must have at least ROLE_AG_USER
            return false;
        }

        // user must have permission to view the article
        return $this->canView($wikiAccess, $token);
    }
}