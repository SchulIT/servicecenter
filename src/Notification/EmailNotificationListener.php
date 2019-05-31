<?php

namespace App\Notification;

use App\Entity\NotificationSetting;
use App\Entity\Problem;
use App\Entity\ProblemType;
use App\Entity\Room;
use App\Event\NewProblemEvent;
use App\Event\ProblemEvents;
use App\Repository\NotificationSettingRepository;
use App\Repository\NotificationSettingRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EmailNotificationListener implements EventSubscriberInterface {

    private $from;
    private $mailer;
    private $notificationSettingRepository;
    private $twig;
    private $logger;

    public function __construct(string $from, \Swift_Mailer $mailer, NotificationSettingRepositoryInterface $notificationSettingRepository, \Twig_Environment $twig, LoggerInterface $logger = null) {
        $this->from = $from;
        $this->mailer = $mailer;
        $this->notificationSettingRepository = $notificationSettingRepository;
        $this->twig = $twig;
        $this->logger = $logger ?? new NullLogger();
    }

    public function onProblemCreated(NewProblemEvent $event) {
        $this->logger->debug('EmailNotificationListener::onProblemCreated() called');

        $problem = $event->getProblem();

        /** @var NotificationSetting[] $notificationSettings */
        $notificationSettings = $this->notificationSettingRepository
            ->findAll();

        foreach($notificationSettings as $notificationSetting) {
            try {
                if($this->mustSendNotification($problem, $notificationSetting) === true) {
                    $body = $this->twig->render('emails/new_problem.html.twig', [
                        'problem' => $problem,
                        'user' => $notificationSetting->getUser()
                    ]);

                    $message = (new \Swift_Message())
                        ->setSubject('[SC] Neues Problem')
                        ->setTo($notificationSetting->getEmail())
                        ->setFrom($this->from)
                        ->setSender($this->from)
                        ->setBody($body);

                    $this->mailer->send($message);
                }
            } catch(\Exception $e) {
                $this->logger
                    ->critical('Failed to send notification email', [
                        'exception' => $e
                    ]);
            }
        }
    }

    private function mustSendNotification(Problem $problem, NotificationSetting $notificationSetting) {
        if($notificationSetting->isEnabled() !== true) {
            return false;
        }

        $roomId = $problem->getDevice()->getRoom()->getId();
        $roomIds = array_map(function(Room $room) {
            return $room->getId();
        }, $notificationSetting->getRooms()->toArray());

        if(!in_array($roomId, $roomIds)) {
            return false;
        }

        $typeId = $problem->getProblemType()->getId();
        $typeIds = array_map(function(ProblemType $type) {
            return $type->getId();
        }, $notificationSetting->getProblemTypes()->toArray());

        if(!in_array($typeId, $typeIds)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            NewProblemEvent::class => 'onProblemCreated'
        ];
    }
}