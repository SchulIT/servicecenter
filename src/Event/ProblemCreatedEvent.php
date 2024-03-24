<?php

namespace App\Event;

use App\Entity\Problem;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class ProblemCreatedEvent extends Event {

    public function __construct(private readonly Problem $problem, private readonly ?User $initiator)
    {
    }

    public function getProblem(): Problem {
        return $this->problem;
    }

    public function getInitiator(): ?User {
        return $this->initiator;
    }
}