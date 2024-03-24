<?php

namespace App\Event;

use App\Entity\Problem;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class ProblemUpdatedEvent extends Event {

    public function __construct(private readonly Problem $problem, private readonly array $changeset, private readonly ?User $initiator) {    }

    public function getProblem(): Problem {
        return $this->problem;
    }

    public function getChangeset(): array {
        return $this->changeset;
    }

    public function getInitiator(): ?User {
        return $this->initiator;
    }
}