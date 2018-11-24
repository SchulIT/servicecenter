<?php

namespace App\Repository;

use App\Entity\Comment;

interface CommentRepositoryInterface {
    public function persist(Comment $comment);

    public function remove(Comment $comment);
}