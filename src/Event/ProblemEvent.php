<?php

namespace App\Event;

use App\Entity\Problem;
use Symfony\Component\EventDispatcher\Event;

class ProblemEvent extends Event {
    private $problem;

    public function __construct(Problem $problem) {
        $this->problem = $problem;
    }

    public function getProblem() {
        return $this->problem;
    }
}