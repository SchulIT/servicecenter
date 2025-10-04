<?php

declare(strict_types=1);

namespace App\Converter;

use App\Entity\Problem;

class ProblemConverter {

    public function convert(Problem $problem): string {
        return sprintf(
            '%s (%s): %s',
            $problem->getDevice()->getName(),
            $problem->getDevice()->getRoom()->getName(),
            $problem->getProblemType()->getName()
        );
    }
}
