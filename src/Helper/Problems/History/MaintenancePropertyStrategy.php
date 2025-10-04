<?php

declare(strict_types=1);

namespace App\Helper\Problems\History;

use Override;
use App\Entity\User;

class MaintenancePropertyStrategy extends AbstractDefaultPropertyStrategy {

    #[Override]
    protected function getPropertyName(): string {
        return 'isMaintenance';
    }

    #[Override]
    public function getText(?User $user, string $username, $value): string {
        $id = $value === true ? 'problems.history.maintenance.on' : 'problems.history.maintenance.off';

        return $this->translator->trans($id, [ '%user%' => $this->getUserDisplayName($user, $username) ]);
    }
}
