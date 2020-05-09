<?php

namespace App\Helper\Problems\History;

use App\Entity\User;

class MaintenancePropertyStrategy extends AbstractDefaultPropertyStrategy {

    protected function getPropertyName(): string {
        return 'isMaintenance';
    }

    public function getText(?User $user, string $username, $value): string {
        if($value === true) {
            $id = 'problems.history.maintenance.on';
        } else {
            $id = 'problems.history.maintenance.off';
        }

        return $this->translator->trans($id, [ '%user%' => $this->getUserDisplayName($user, $username) ]);
    }
}