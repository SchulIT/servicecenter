<?php

namespace App\Event;

use App\Entity\Problem;
use Symfony\Contracts\EventDispatcher\Event;

class ProblemUpdatedEvent extends Event {
    private $problem;
    private $changeset;

    public function __construct(Problem $problem, array $changeset) {
        $this->problem = $problem;
        $this->changeset = $changeset;
    }

    public function getProblem(): Problem {
        return $this->problem;
    }

    public function getChangeset(): array {
        return $this->changeset;
    }
}