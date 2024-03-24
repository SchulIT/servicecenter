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
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

readonly class EmailNotificationListener implements EventSubscriberInterface {

    public function __construct(private string $sender, private string $appName, private Environment $twig, private NotificationSettingRepositoryInterface $notificationSettingRepository, private ProblemConverter $problemConverter, private ChangesetHelper $changesetHelper, private HistoryResolver $historyResolver, private MailerInterface $mailer, private TranslatorInterface $translator, private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function onProblemCreated(ProblemCreatedEvent $event): void {
        $problem = $event->getProblem();

        /** @var NotificationSetting[] $notificationSettings */
        $notificationSettings = $this->notificationSettingRepository
            ->findAll();

        foreach($notificationSettings as $notificationSetting) {
            if($event->getInitiator() != null && $event->getInitiator()->getId() === $notificationSetting->getUser()->getId()) {
                // prevent sending notification to the same user who initiated the update
                continue;
            }

            if($this->mustSendNotification($problem, $notificationSetting) === true) {
                $email = (new Email())
                    ->subject($this->translator->trans('problem.new.subject', ['%problem%' => $this->problemConverter->convert($problem)], 'mail'))
                    ->from(new Address($this->sender, $this->appName))
                    ->sender(new Address($this->sender, $this->appName))
                    ->to($notificationSetting->getUser()->getEmail())
                    ->text(
                        $this->twig->render(
                            'emails/new_problem.txt.twig',
                            [
                                'problem' => $problem,
                                'user' => $notificationSetting->getUser(),
                                'link' => $this->urlGenerator->generate('show_problem', [ 'uuid' => $problem->getUuid()->toString() ], UrlGeneratorInterface::ABSOLUTE_URL)
                            ]
                        )
                    );

                $this->mailer->send($email);
            }
        }
    }

    public function onProblemUpdated(ProblemUpdatedEvent $event): void {
        $problem = $event->getProblem();
        $participants = $this->getParticipants($problem);

        $changes = $this->changesetHelper->getHumanReadableChangeset($event->getChangeset());

        foreach($participants as $participant) {
            if(empty($participant->getEmail())) {
                continue;
            }

            if($event->getInitiator() != null && $event->getInitiator()->getId() === $participant->getId()) {
                // prevent status changed sent to the same user who initiated the update
                continue;
            }

            $email = (new Email())
                ->subject($this->translator->trans('problem.updated.subject', ['%problem%' => $this->problemConverter->convert($problem)], 'mail'))
                ->from(new Address($this->sender, $this->appName))
                ->sender(new Address($this->sender, $this->appName))
                ->to($participant->getEmail())
                ->text(
                    $this->twig->render(
                        'emails/problem_updated.txt.twig',
                        [
                            'problem' => $problem,
                            'changes' => $changes,
                            'user' => $participant,
                            'link' => $this->urlGenerator->generate('show_problem', [ 'uuid' => $problem->getUuid()->toString() ], UrlGeneratorInterface::ABSOLUTE_URL)
                        ]
                    )
                );

            $this->mailer->send($email);
        }
    }

    public function onCommentCreated(CommentCreatedEvent $event): void {
        $problem = $event->getProblem();
        $participants = $this->getParticipants($problem);

        foreach($participants as $participant) {
            if(empty($participant->getEmail())) {
                continue;
            }

            if($event->getComment()->getCreatedBy()->getId() === $participant->getId()) {
                // prevent status changed sent to the same user who created the comment
                continue;
            }

            $email = (new Email())
                ->subject($this->translator->trans('problem.comment.subject', ['%problem%' => $this->problemConverter->convert($problem)], 'mail'))
                ->from(new Address($this->sender, $this->appName))
                ->sender(new Address($this->sender, $this->appName))
                ->to($participant->getEmail())
                ->text(
                    $this->twig->render(
                        'emails/new_comment.txt.twig',
                        [
                            'problem' => $problem,
                            'user' => $participant,
                            'author' => $event->getComment()->getCreatedBy(),
                            'comment' => $event->getComment(),
                            'link' => $this->urlGenerator->generate('show_problem', [ 'uuid' => $problem->getUuid()->toString() ], UrlGeneratorInterface::ABSOLUTE_URL)
                        ]
                    )
                );

            $this->mailer->send($email);
        }
    }

    /**
     * @return User[]
     */
    private function getParticipants(Problem $problem): array {
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

    private function mustSendNotification(Problem $problem, NotificationSetting $notificationSetting): bool {
        if($notificationSetting->isEnabled() !== true) {
            return false;
        }

        $roomId = $problem->getDevice()->getRoom()->getId();
        $roomIds = array_map(fn(Room $room) => $room->getId(), $notificationSetting->getRooms()->toArray());

        if(!in_array($roomId, $roomIds)) {
            return false;
        }

        $typeId = $problem->getProblemType()->getId();
        $typeIds = array_map(fn(ProblemType $type) => $type->getId(), $notificationSetting->getProblemTypes()->toArray());

        if(!in_array($typeId, $typeIds)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array {
        return [
            ProblemCreatedEvent::class => 'onProblemCreated',
            ProblemUpdatedEvent::class => 'onProblemUpdated',
            CommentCreatedEvent::class => 'onCommentCreated'
        ];
    }
}