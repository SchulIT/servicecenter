<?php

namespace App\Event;

use App\Entity\Comment;
use App\Entity\Problem;
use Symfony\Contracts\EventDispatcher\Event;

class CommentCreatedEvent extends Event {
    private $comment;

    public function __construct(Comment $comment) {
        $this->comment = $comment;
    }

    public function getProblem(): Problem {
        return $this->comment->getProblem();
    }

    public function getComment(): Comment {
        return $this->comment;
    }
}