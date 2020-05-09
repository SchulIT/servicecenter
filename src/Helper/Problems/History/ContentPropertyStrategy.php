<?php

namespace App\Helper\Problems\History;

use App\Entity\User;

class ContentPropertyStrategy extends AbstractDefaultPropertyStrategy {

    public function getText(?User $user, string $username, $value): string {
        return $this->translator->trans('problems.history.content', [
            '%user%' => $this->getUserDisplayName($user, $username)
        ]);
    }

    protected function getPropertyName(): string {
        return 'content';
    }
}