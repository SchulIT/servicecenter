<?php

namespace App\Helper\Problems\History;

use App\Entity\User;

class StatusPropertyStrategy extends AbstractDefaultPropertyStrategy {

    protected function getPropertyName(): string {
        return 'isOpen';
    }

    public function getText(?User $user, string $username, $value): string {
        if($value === true) {
            $id = 'problems.history.status.open';
        } else {
            $id = 'problems.history.status.closed';
        }

        return $this->translator->trans($id, [ '%user%' => $this->getUserDisplayName($user, $username) ]);
    }
}