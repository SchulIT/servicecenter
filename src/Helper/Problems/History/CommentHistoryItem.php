<?php

namespace App\Helper\Problems\History;

use App\Entity\Comment;

class CommentHistoryItem implements HistoryItemInterface {

    private $comment;

    public function __construct(Comment $comment) {
        $this->comment = $comment;
    }

    public function getComment(): Comment {
        return $this->comment;
    }

    public function getDateTime(): \DateTime {
        return $this->comment->getCreatedAt();
    }
}