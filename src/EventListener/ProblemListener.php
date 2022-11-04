<?php

namespace App\EventListener;

use App\Entity\Comment;
use App\Entity\Problem;
use App\Entity\User;
use App\Event\CommentCreatedEvent;
use App\Event\ProblemCreatedEvent;
use App\Event\ProblemUpdatedEvent;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Listener which "translates" any new problems added through the ORM into an dispatched event.
 */
class ProblemListener implements EventSubscriber {

    private EventDispatcherInterface $eventDispatcher;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EventDispatcherInterface $eventDispatcher, TokenStorageInterface  $tokenStorage) {
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenStorage = $tokenStorage;
    }

    public function onFlush(OnFlushEventArgs $args) {
        $uow = $args->getEntityManager()->getUnitOfWork();

        $user = null;
        $token = $this->tokenStorage->getToken();

        if($token !== null) {
            $user = $token->getUser();

            if(!$user instanceof User) {
                $user = null;
            }
        }

        foreach($uow->getScheduledEntityInsertions() as $entity) {
            if($entity instanceof Problem) {
                $this->eventDispatcher
                    ->dispatch(new ProblemCreatedEvent($entity, $user));
            } else if($entity instanceof Comment) {
                $this->eventDispatcher
                    ->dispatch(new CommentCreatedEvent($entity));
            }
        }

        foreach($uow->getScheduledEntityUpdates() as $entity) {
            if($entity instanceof Problem) {
                $this->eventDispatcher
                    ->dispatch(new ProblemUpdatedEvent($entity, $uow->getEntityChangeSet($entity), $user));
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents(): array {
        return [
            Events::onFlush
        ];
    }
}