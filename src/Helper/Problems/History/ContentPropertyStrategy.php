<?php

namespace App\Helper\Problems\History;

use App\Entity\User;

class ContentPropertyStrategy extends AbstractDefaultPropertyStrategy {

    public function getText(User $user, $value): string {
        return $this->translator->trans('problems.history.content', [
            '%user%' => $user
        ]);
    }

    protected function getPropertyName(): string {
        return 'content';
    }
}