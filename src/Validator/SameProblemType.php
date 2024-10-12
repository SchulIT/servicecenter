<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class SameProblemType extends Constraint {
    public $message = 'Devices must be of the same device type.';
}