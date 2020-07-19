<?php

namespace App\Helper\Problems\Changeset;

use App\Converter\PriorityConverter;
use App\Entity\Priority;
use App\Entity\ProblemType;
use App\Entity\User;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChangesetHelper {
    private $translator;
    private $priorityConverter;

    public function __construct(TranslatorInterface $translator, PriorityConverter $priorityConverter) {
        $this->translator = $translator;
        $this->priorityConverter = $priorityConverter;
    }

    public function getHumanReadableChangeset(array $changeset) {
        $map = [
            'priority' => 'getPriorityChanget',
            'content' => 'getContentChangeset',
            'problemType' => 'getProblemTypeChangeset',
            'isMaintenance' => 'getMaintenanceChangeset',
            'isOpen' => 'getStatusChangeset',
            'assignee' => 'getAssigneeChangeset'
        ];

        $result = [ ];

        foreach($changeset as $property => $values) {
            $oldValue = $values[0];
            $newValue = $values[1];

            $callback = $map[$property] ?? null;

            if($callback !== null && method_exists($this, $callback)) {
                $result[] = call_user_func([$this, $callback], $oldValue, $newValue);
            }
        }

        return $result;
    }

    private function getPriorityChangeset(Priority $oldPriority, Priority $newPriority) {
        return $this->translator->trans('problems.changeset.priority', [
            '%old%' => $this->priorityConverter->convert($oldPriority),
            '%new%' => $this->priorityConverter->convert($newPriority)
        ]);
    }

    private function getContentChange(string $oldContent, string $newContent) {
        return $this->translator->trans('problems.changeset.content');
    }

    private function getProblemTypeChangeset(ProblemType $oldType, ProblemType $newType) {
        return $this->translator->trans('problems.changeset.type', [
            '%old%' => $oldType->getName(),
            '%new%' => $newType->getName()
        ]);
    }

    private function getMaintenanceChangeset(bool $oldMaintenance, bool $newMaintenance) {
        return $this->translator->trans('problems.changeset.maintenance', [
            '%old%' => $this->translator->trans($oldMaintenance ? 'label.yes' : 'label.no'),
            '%new%' => $this->translator->trans($newMaintenance ? 'label.yes' : 'label.no')
        ]);
    }

    private function getStatusChangeset(bool $oldIsOpen, bool $newIsOpen) {
        return $this->translator->trans('problems.changeset.is_open', [
            '%old%' => $this->translator->trans($oldIsOpen ? 'status.open' : 'status.closed' ),
            '%new%' => $this->translator->trans($newIsOpen ? 'status.open' : 'status.closed' )
        ]);
    }

    private function getAssigneeChangeset(?User $oldAssignee, ?User $newAssignee) {
        return $this->translator->trans('problems.changeset.assignee', [
            '%old%' => $oldAssignee !== null ? (string)$oldAssignee : $this->translator->trans('problems.assignee.none'),
            '%new%' => $newAssignee !== null ? (string)$newAssignee : $this->translator->trans('problems.assignee.none')
        ]);
    }
}