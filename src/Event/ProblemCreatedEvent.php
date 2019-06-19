<?php

namespace App\Event;

use App\Entity\Problem;
use Symfony\Contracts\EventDispatcher\Event;

class ProblemCreatedEvent extends Event {

    private $problem;

    public function __construct(Problem $problem) {
        $this->problem = $problem;
    }

    public function getProblem(): Problem {
        return $this->problem;
    }
}