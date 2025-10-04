<?php

declare(strict_types=1);

namespace App\Helper\Problems;

use App\Entity\Problem;

interface BulkActionInterface {
    /**
     * @return bool True if action was performed successfully, false otherwise
     */
    public function performAction(Problem $problem): bool;

    public function getName(): string;
}
