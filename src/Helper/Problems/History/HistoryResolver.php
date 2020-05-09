<?php

namespace App\Helper\Problems\History;

use App\Converter\PriorityConverter;
use App\Entity\Problem;
use App\Entity\User;
use App\Form\ProblemType;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Loggable\Entity\LogEntry;
use Psr\Log\LoggerInterface;

class HistoryResolver {
    private $em;
    private $logger;

    /** @var PropertyValueStrategyInterface[] */
    private $propertyValueStrategies = [ ];

    /**
     * HistoryResolver constructor.
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     * @param PropertyValueStrategyInterface[] $strategies
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger, iterable $strategies) {
        $this->em = $em;
        $this->logger = $logger;

        foreach($strategies as $strategy) {
            $this->propertyValueStrategies[] = $strategy;
        }
    }

    /**
     * @param Problem $problem
     * @return User[]
     */
    public function resolveParticipants(Problem $problem) {
        /** @var LogEntry[] $logEntries */
        $logEntries = $this->em->getRepository(LogEntry::class)
            ->getLogEntries($problem);

        $usernames = array_unique(
            array_map(function(LogEntry $entry) {
                return $entry->getUsername();
            }, $logEntries)
        );

        $users = [ ];

        foreach($usernames as $username) {
            $user = $this->em->getRepository(User::class)
                ->findOneBy(['username' => $username ]);

            if($user !== null) {
                $users[] = $user;
            }
        }

        return $users;
    }

    /**
     * @param Problem $problem
     * @return HistoryItemInterface[]
     */
    public function resolveHistory(Problem $problem) {
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
                                $entry->getLoggedAt(),
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

        usort($history, function(HistoryItemInterface $itemA, HistoryItemInterface $itemB) {
            if($itemA->getDateTime() === $itemB->getDateTime()) {
                return 0;
            }

            return $itemA->getDateTime() < $itemB->getDateTime() ? -1 : 1;
        });

        return $history;
    }
}