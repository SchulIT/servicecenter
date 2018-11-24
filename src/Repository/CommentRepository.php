<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;

class CommentRepository implements CommentRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function persist(Comment $comment) {
        $this->em->persist($comment);
        $this->em->flush();
    }

    public function remove(Comment $comment) {
        $this->em->remove($comment);
        $this->em->flush();
    }
}