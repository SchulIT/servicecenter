<?php

namespace App\Event;

use App\Entity\Problem;
use Symfony\Contracts\EventDispatcher\Event;

class NewProblemEvent extends Event {

    private $problem;

    public function __construct(Problem $problem) {
        $this->problem = $problem;
    }

    public function getProblem() {
        return $this->problem;
    }
}