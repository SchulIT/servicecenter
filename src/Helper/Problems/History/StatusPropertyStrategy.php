<?php

declare(strict_types=1);

namespace App\Helper\Problems\History;

use Override;
use App\Entity\User;

class StatusPropertyStrategy extends AbstractDefaultPropertyStrategy {

    #[Override]
    protected function getPropertyName(): string {
        return 'isOpen';
    }

    #[Override]
    public function getText(?User $user, string $username, $value): string {
        $id = $value === true ? 'problems.history.status.open' : 'problems.history.status.closed';

        return $this->translator->trans($id, [ '%user%' => $this->getUserDisplayName($user, $username) ]);
    }
}
