<?php

declare(strict_types=1);

namespace App\Helper\Problems\History;

use Override;
use DateTime;
use App\Entity\Comment;

readonly class CommentHistoryItem implements HistoryItemInterface {

    public function __construct(private Comment $comment)
    {
    }

    public function getComment(): Comment {
        return $this->comment;
    }

    #[Override]
    public function getDateTime(): DateTime {
        return $this->comment->getCreatedAt();
    }
}
