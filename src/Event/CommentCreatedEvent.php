<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Comment;
use App\Entity\Problem;
use Symfony\Contracts\EventDispatcher\Event;

class CommentCreatedEvent extends Event {
    public function __construct(private readonly Comment $comment)
    {
    }

    public function getProblem(): Problem {
        return $this->comment->getProblem();
    }

    public function getComment(): Comment {
        return $this->comment;
    }
}
