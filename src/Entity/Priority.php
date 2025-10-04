<?php

declare(strict_types=1);

namespace App\Entity;

enum Priority: string {
    case Normal = 'normal';
    case High = 'high';
    case Critical = 'critical';
}
