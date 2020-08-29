<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SameProblemType extends Constraint {
    public $message = 'Devices must be of the same device type.';
}