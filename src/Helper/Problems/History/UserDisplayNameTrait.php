<?php

namespace App\Helper\Problems\History;

use App\Entity\User;

trait UserDisplayNameTrait {
    protected function getUserDisplayName(?User $user, string $username): string {
        if($user === null) {
            return $username;
        }

        return (string)$user;
    }
}