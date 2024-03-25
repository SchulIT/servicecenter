<?php

namespace App\EventListener;

use Shapecode\Bundle\CronBundle\Domain\CronJobMetadata;
use Shapecode\Bundle\CronBundle\Event\LoadJobsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Command\ConsumeMessagesCommand;

readonly class LoadMessengerCronJobs implements EventSubscriberInterface {

    public function __construct(private bool $useCronjobForMessenger, private ConsumeMessagesCommand $command) {

    }

    public function onLoadJobs(LoadJobsEvent $event): void {
        if(!$this->useCronjobForMessenger) {
            return;
        }

        $event->addJob(CronJobMetadata::createByCommand('*/1 * * * *', $this->command, 'async -vv --time-limit=20 --no-reset'));
        $event->addJob(CronJobMetadata::createByCommand('*/1 * * * *', $this->command, 'mail -vv --time-limit=20 --no-reset'));
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array {
        return [
            LoadJobsEvent::class => 'onLoadJobs',
        ];
    }
}