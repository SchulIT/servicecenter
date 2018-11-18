<?php

namespace App\Helper\Problems;

use App\Entity\Problem;

interface BulkActionInterface {
    /**
     * @param Problem $problem
     * @return bool True if action was performed successfully, false otherwise
     */
    public function performAction(Problem $problem): bool;

    /**
     * @return string
     */
    public function getName(): string;
}