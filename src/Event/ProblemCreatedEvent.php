<?php

namespace App\Event;

use App\Entity\Problem;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class ProblemCreatedEvent extends Event {

    private $problem;

    private $initiator;

    public function __construct(Problem $problem, ?User $initiator) {
        $this->problem = $problem;
        $this->initiator = $initiator;
    }

    public function getProblem(): Problem {
        return $this->problem;
    }

    /**
     * @return User|null
     */
    public function getInitiator(): ?User {
        return $this->initiator;
    }
}