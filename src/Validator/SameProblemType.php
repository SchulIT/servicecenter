<?php

declare(strict_types=1);

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class SameProblemType extends Constraint {
    public string $message = 'Devices must be of the same device type.';
}
