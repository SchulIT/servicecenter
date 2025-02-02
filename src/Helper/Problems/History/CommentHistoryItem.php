<?php

namespace App\Helper\Problems\History;

use DateTime;
use App\Entity\Comment;

readonly class CommentHistoryItem implements HistoryItemInterface {

    public function __construct(private Comment $comment)
    {
    }

    public function getComment(): Comment {
        return $this->comment;
    }

    public function getDateTime(): DateTime {
        return $this->comment->getCreatedAt();
    }
}