<?php

declare(strict_types=1);

namespace App\Repository;

use Override;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;

class CommentRepository implements CommentRepositoryInterface {

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    #[Override]
    public function persist(Comment $comment): void {
        $this->em->persist($comment);
        $this->em->flush();
    }

    #[Override]
    public function remove(Comment $comment): void {
        $this->em->remove($comment);
        $this->em->flush();
    }
}
