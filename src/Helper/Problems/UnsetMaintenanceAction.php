<?php

namespace App\Helper\Problems;

use App\Entity\Problem;
use App\Security\Voter\ProblemVoter;

class UnsetMaintenanceAction extends AbstractBulkAction {

    protected function getAttributes() {
        return ProblemVoter::MAINTENANCE;
    }

    protected function perform(Problem $problem): bool {
        $problem->setIsMaintenance(false);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string {
        return 'unset_maintenance';
    }
}