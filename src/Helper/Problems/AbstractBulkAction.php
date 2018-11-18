<?php

namespace App\Helper\Problems;

use App\Entity\Problem;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

abstract class AbstractBulkAction implements BulkActionInterface {

    protected $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker) {
        $this->authorizationChecker = $authorizationChecker;
    }

    protected abstract function getAttributes();

    protected abstract function perform(Problem $problem): bool;

    /**
     * @inheritDoc
     */
    public function performAction(Problem $problem): bool {
        if($this->authorizationChecker->isGranted($this->getAttributes(), $problem)) {
            return $this->perform($problem);
        }

        return false;
    }
}