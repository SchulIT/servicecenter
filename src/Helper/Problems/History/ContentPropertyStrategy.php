<?php

declare(strict_types=1);

namespace App\Helper\Problems\History;

use Override;
use App\Entity\User;

class ContentPropertyStrategy extends AbstractDefaultPropertyStrategy {

    #[Override]
    public function getText(?User $user, string $username, $value): string {
        return $this->translator->trans('problems.history.content', [
            '%user%' => $this->getUserDisplayName($user, $username)
        ]);
    }

    #[Override]
    protected function getPropertyName(): string {
        return 'content';
    }
}
