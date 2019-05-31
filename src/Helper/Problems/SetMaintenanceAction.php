<?php

namespace App\Helper\Problems;

use App\Entity\Problem;
use App\Security\Voter\ProblemVoter;

class SetMaintenanceAction extends AbstractBulkAction {

    protected function getAttributes() {
        return ProblemVoter::MAINTENANCE;
    }

    protected function perform(Problem $problem): bool {
        $problem->setIsMaintenance(true);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string {
        return 'set_maintenance';
    }
}