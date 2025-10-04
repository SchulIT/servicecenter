<?php

declare(strict_types=1);

namespace App\Helper\Problems;

use Override;
use App\Entity\Problem;
use App\Security\Voter\ProblemVoter;

class UnsetMaintenanceAction extends AbstractBulkAction {

    #[Override]
    protected function getAttributes(): string {
        return ProblemVoter::MAINTENANCE;
    }

    #[Override]
    protected function perform(Problem $problem): bool {
        $problem->setIsMaintenance(false);

        return true;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getName(): string {
        return 'unset_maintenance';
    }
}
