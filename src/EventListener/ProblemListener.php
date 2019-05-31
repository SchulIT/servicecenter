<?php

namespace App\EventListener;

use App\Entity\Problem;
use App\Event\NewProblemEvent;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Listener which "translates" any new problems added through the ORM into an dispatched event.
 */
class ProblemListener implements EventSubscriber {

    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher) {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function onFlush(OnFlushEventArgs $args) {
        $uow = $args->getEntityManager()->getUnitOfWork();

        foreach($uow->getScheduledEntityInsertions() as $entity) {
            if($entity instanceof Problem) {
                $this
                    ->eventDispatcher
                    ->dispatch(new NewProblemEvent($entity));
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents() {
        return [
            Events::onFlush
        ];
    }
}