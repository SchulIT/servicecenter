<?php

namespace App\Helper\Problems\History;

use App\Converter\PriorityConverter;
use App\Entity\Problem;
use App\Entity\User;
use App\Form\ProblemType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Loggable\Entity\LogEntry;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class HistoryResolver {
    /** @var PropertyValueStrategyInterface[] */
    private array $propertyValueStrategies = [ ];

    /**
     * HistoryResolver constructor.
     * @param PropertyValueStrategyInterface[] $strategies
     */
    public function __construct(private readonly EntityManagerInterface $em, #[AutowireIterator('app.problem_history_value_strategy')] iterable $strategies) {
        foreach($strategies as $strategy) {
            $this->propertyValueStrategies[] = $strategy;
        }
    }

    /**
     * @return User[]
     */
    public function resolveParticipants(Problem $problem): array {
        /** @var LogEntry[] $logEntries */
        $logEntries = $this->em->getRepository(LogEntry::class)
            ->getLogEntries($problem);

        $usernames = array_unique(
            array_map(fn(LogEntry $entry) => $entry->getUsername(), $logEntries)
        );

        $users = [ ];

        foreach($usernames as $username) {
            $user = $this->em->getRepository(User::class)
                ->findOneBy(['username' => $username ]);

            if($user !== null) {
                $users[$user->getId()] = $user;
            }
        }

        foreach($this->resolveHistory($problem) as $historyItem) {
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

    /**
     * @return HistoryItemInterface[]
     */
    public function resolveHistory(Problem $problem): array {
        /** @var HistoryItemInterface[] $history */
        $history = [ ];

        /** @var LogEntry[] $logEntries */
        $logEntries = $this->em->getRepository(LogEntry::class)
            ->getLogEntries($problem);

        foreach($logEntries as $entry) {
            if($entry->getAction() === 'update') {
                $user = $this->em->getRepository(User::class)
                    ->findOneBy(['username' => $entry->getUsername() ]);

                $data = $entry->getData();

                foreach($data as $property => $value) {
                    foreach($this->propertyValueStrategies as $strategy) {
                        if($strategy->supportsProperty($property)) {
                            $history[] = new PropertyChangedHistoryItem(
                                $property,
                                DateTime::createFromInterface($entry->getLoggedAt()),
                                $user,
                                $entry->getUsername(),
                                $strategy->getValue($value),
                                $strategy->getText($user, $entry->getUsername(), $value)
                            );
                        }
                    }
                }
            }
        }

        foreach($problem->getComments() as $comment) {
            $history[] = new CommentHistoryItem($comment);
        }

        usort($history, fn(HistoryItemInterface $itemA, HistoryItemInterface $itemB) => $itemA->getDateTime() <=> $itemB->getDateTime());

        return $history;
    }
}