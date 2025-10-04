<?php

declare(strict_types=1);

namespace App\Helper\Problems\History;

use App\Entity\User;

trait UserDisplayNameTrait {
    protected function getUserDisplayName(?User $user, string $username): string {
        if(!$user instanceof User) {
            return $username;
        }

        return (string)$user;
    }
}
