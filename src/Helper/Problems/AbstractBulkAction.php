<?php

namespace App\Helper\Problems;

use App\Entity\Problem;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

abstract class AbstractBulkAction implements BulkActionInterface {

    public function __construct(private readonly AuthorizationCheckerInterface $authorizationChecker) {    }

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