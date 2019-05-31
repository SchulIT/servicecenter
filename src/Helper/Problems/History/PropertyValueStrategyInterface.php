<?php

namespace App\Helper\Problems\History;

use App\Entity\User;

interface PropertyValueStrategyInterface {

    public function supportsProperty(string $name): bool;

    public function getValue($value);

    public function getText(User $user, $value): string;
}