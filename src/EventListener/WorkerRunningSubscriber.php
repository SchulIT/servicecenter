<?php

declare(strict_types=1);

namespace App\EventListener;

use Override;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;

readonly class WorkerRunningSubscriber implements EventSubscriberInterface {

    public function __construct(private bool $useCronjobForMessenger) {

    }

    public function onWorkerRunning(WorkerRunningEvent $runningEvent): void {
        if(!$this->useCronjobForMessenger) {
            return;
        }

        if($runningEvent->isWorkerIdle()) {
            $runningEvent->getWorker()->stop();
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function getSubscribedEvents(): array {
        return [
            WorkerRunningEvent::class => 'onWorkerRunning'
        ];
    }
}
