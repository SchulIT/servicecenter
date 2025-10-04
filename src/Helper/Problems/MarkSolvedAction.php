<?php

declare(strict_types=1);

namespace App\Helper\Problems;

use Override;
use App\Entity\Problem;
use App\Security\Voter\ProblemVoter;

class MarkSolvedAction extends AbstractBulkAction {

    #[Override]
    protected function getAttributes(): string {
        return ProblemVoter::STATUS;
    }

    #[Override]
    protected function perform(Problem $problem): bool {
        $problem->setIsOpen(false);

        return true;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getName(): string {
        return 'mark_solved';
    }
}
