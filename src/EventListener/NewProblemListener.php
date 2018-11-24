<?php

namespace App\EventListener;

use App\Entity\Problem;
use App\Event\ProblemEvent;
use App\Event\ProblemEvents;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Listener which "translates" any new problems added through the ORM into an dispatched event.
 */
class NewProblemListener implements EventSubscriber {

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
                    ->dispatch(ProblemEvents::NEW_PROBLEM, new ProblemEvent($entity));
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