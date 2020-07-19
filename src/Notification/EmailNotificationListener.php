<?php

namespace App\Notification;

use App\Converter\ProblemConverter;
use App\Entity\NotificationSetting;
use App\Entity\Problem;
use App\Entity\ProblemType;
use App\Entity\Room;
use App\Entity\User;
use App\Event\CommentCreatedEvent;
use App\Event\ProblemCreatedEvent;
use App\Event\ProblemUpdatedEvent;
use App\Helper\Problems\Changeset\ChangesetHelper;
use App\Helper\Problems\History\CommentHistoryItem;
use App\Helper\Problems\History\HistoryResolver;
use App\Helper\Problems\History\PropertyChangedHistoryItem;
use App\Repository\NotificationSettingRepositoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class EmailNotificationListener implements EventSubscriberInterface {

    private $from;
    private $mailer;
    private $notificationSettingRepository;
    private $historyResolver;
    private $changesetHelper;
    private $twig;
    private $translator;
    private $problemConverter;
    private $logger;

    public function __construct(string $from, \Swift_Mailer $mailer, NotificationSettingRepositoryInterface $notificationSettingRepository,
                                HistoryResolver $historyResolver, ChangesetHelper $changesetHelper, Environment $twig, TranslatorInterface $translator,
                                ProblemConverter $problemConverter, LoggerInterface $logger) {
        $this->from = $from;
        $this->mailer = $mailer;
        $this->notificationSettingRepository = $notificationSettingRepository;
        $this->historyResolver = $historyResolver;
        $this->changesetHelper = $changesetHelper;
        $this->twig = $twig;
        $this->translator = $translator;
        $this->problemConverter = $problemConverter;
        $this->logger = $logger;
    }

    public function onProblemCreated(ProblemCreatedEvent $event) {
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
                        ->setSubject($this->translator->trans('problem.new.subject', ['%problem%' => $this->problemConverter->convert($problem)], 'mail'))
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

    public function onProblemUpdated(ProblemUpdatedEvent $event) {
        $problem = $event->getProblem();
        $participants = $this->getParticipants($problem);

        $changes = $this->changesetHelper->getHumanReadableChangeset($event->getChangeset());

        foreach($participants as $participant) {
            if(!empty($participant->getEmail())) {
                continue;
            }

            try {
                $body = $this->twig->render('emails/problem_updated.html.twig', [
                    'problem' => $problem,
                    'changes' => $changes,
                    'user' => $participant
                ]);

                $message = (new \Swift_Message())
                    ->setSubject($this->translator->trans('problem.updated.subject', ['%problem%' => $this->problemConverter->convert($problem)], 'mail'))
                    ->setTo($participant->getEmail())
                    ->setFrom($this->from)
                    ->setSender($this->from)
                    ->setBody($body);

                $this->mailer->send($message);
            } catch (\Exception $e) {
                $this->logger
                    ->critical('Failed to send notification email', [
                        'exception' => $e
                    ]);
            }
        }
    }

    public function onCommentCreated(CommentCreatedEvent $event) {
        $problem = $event->getProblem();
        $participants = $this->getParticipants($problem);

        foreach($participants as $participant) {
            if(!empty($participant->getEmail())) {
                continue;
            }

            try {
                $body = $this->twig->render('emails/new_comment.html.twig', [
                    'problem' => $problem,
                    'user' => $participant,
                    'author' => $event->getComment()->getCreatedBy()
                ]);

                $message = (new \Swift_Message())
                    ->setSubject($this->translator->trans('problem.updated.subject', ['%problem%' => $this->problemConverter->convert($problem)], 'mail'))
                    ->setTo($participant->getEmail())
                    ->setFrom($this->from)
                    ->setSender($this->from)
                    ->setBody($body);

                $this->mailer->send($message);
            } catch (\Exception $e) {
                $this->logger
                    ->critical('Failed to send notification email', [
                        'exception' => $e
                    ]);
            }
        }
    }

    /**
     * @param Problem $problem
     * @return User[]
     */
    private function getParticipants(Problem $problem) {
        $users = [ ];

        $users[$problem->getCreatedBy()->getId()] = $problem->getCreatedBy();

        foreach($this->historyResolver->resolveHistory($problem) as $historyItem) {
            $user = null;

            if($historyItem instanceof PropertyChangedHistoryItem) {
                $user = $historyItem->getUser();
            } else if($historyItem instanceof CommentHistoryItem) {
                $user = $historyItem->getComment()->getCreatedBy();
            }

            if($user !== null) {
                $users[$user->getId()] = $user;
            }
        }

        return array_values($users);
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
            ProblemCreatedEvent::class => 'onProblemCreated',
            ProblemUpdatedEvent::class => 'onProblemUpdated',
            CommentCreatedEvent::class => 'onCommentCreated'
        ];
    }
}