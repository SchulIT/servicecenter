<?php

namespace App\Event;

use App\Entity\Problem;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class ProblemUpdatedEvent extends Event {
    private $problem;
    private $changeset;

    private $initiator;

    public function __construct(Problem $problem, array $changeset, ?User $initiator) {
        $this->problem = $problem;
        $this->changeset = $changeset;
        $this->initiator;
    }

    public function getProblem(): Problem {
        return $this->problem;
    }

    public function getChangeset(): array {
        return $this->changeset;
    }

    /**
     * @return User|null
     */
    public function getInitiator(): ?User {
        return $this->initiator;
    }
}