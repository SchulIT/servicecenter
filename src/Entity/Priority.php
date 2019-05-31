<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static Priority Normal()
 * @method static Priority High()
 * @method static Priority Critical()
 */
class Priority extends Enum {
    private const Normal = 'normal';
    private const High = 'high';
    private const Critical = 'critical';
}