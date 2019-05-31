<?php

namespace App\Helper\Problems;

use App\Entity\Problem;
use App\Security\Voter\ProblemVoter;

class MarkSolvedAction extends AbstractBulkAction {

    protected function getAttributes() {
        return ProblemVoter::STATUS;
    }

    protected function perform(Problem $problem): bool {
        $problem->setIsOpen(false);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string {
        return 'mark_solved';
    }
}