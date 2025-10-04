<?php

declare(strict_types=1);

namespace App\Helper\Problems\History;

use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.problem_history_value_strategy')]
interface PropertyValueStrategyInterface {

    public function supportsProperty(string $name): bool;

    public function getValue($value);

    public function getText(?User $user, string $username, $value): string;
}
