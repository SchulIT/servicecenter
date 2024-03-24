<?php

namespace App\Helper\Problems\Changeset;

use App\Converter\PriorityConverter;
use App\Entity\Priority;
use App\Entity\ProblemType;
use App\Entity\User;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChangesetHelper {
    public function __construct(private readonly TranslatorInterface $translator, private readonly PriorityConverter $priorityConverter)
    {
    }

    public function getHumanReadableChangeset(array $changeset): array {
        $map = [

        ];

        $result = [ ];

        foreach($changeset as $property => $values) {
            $oldValue = $values[0];
            $newValue = $values[1];

            $changeText = match ($property) {
                'priority' => $this->getPriorityChangeset($oldValue, $newValue),
                'content' => $this->getContentChange($oldValue, $newValue),
                'problemType' => $this->getProblemTypeChangeset($oldValue, $newValue),
                'isMaintenance' => $this->getMaintenanceChangeset($oldValue, $newValue),
                'isOpen' => $this->getStatusChangeset($oldValue, $newValue),
                'assignee' => $this->getAssigneeChangeset($oldValue, $newValue),
                default => null
            };

            if($changeText !== null) {
                $result[] = $changeText;
            }
        }

        return $result;
    }

    private function getPriorityChangeset(Priority $oldPriority, Priority $newPriority): string {
        return $this->translator->trans('problems.changeset.priority', [
            '%old%' => $this->priorityConverter->convert($oldPriority),
            '%new%' => $this->priorityConverter->convert($newPriority)
        ]);
    }

    private function getContentChange(string $oldContent, string $newContent): string {
        return $this->translator->trans('problems.changeset.content');
    }

    private function getProblemTypeChangeset(ProblemType $oldType, ProblemType $newType): string {
        return $this->translator->trans('problems.changeset.type', [
            '%old%' => $oldType->getName(),
            '%new%' => $newType->getName()
        ]);
    }

    private function getMaintenanceChangeset(bool $oldMaintenance, bool $newMaintenance): string {
        return $this->translator->trans('problems.changeset.maintenance', [
            '%old%' => $this->translator->trans($oldMaintenance ? 'label.yes' : 'label.no'),
            '%new%' => $this->translator->trans($newMaintenance ? 'label.yes' : 'label.no')
        ]);
    }

    private function getStatusChangeset(bool $oldIsOpen, bool $newIsOpen): string {
        return $this->translator->trans('problems.changeset.is_open', [
            '%old%' => $this->translator->trans($oldIsOpen ? 'status.open' : 'status.closed' ),
            '%new%' => $this->translator->trans($newIsOpen ? 'status.open' : 'status.closed' )
        ]);
    }

    private function getAssigneeChangeset(?User $oldAssignee, ?User $newAssignee): string {
        return $this->translator->trans('problems.changeset.assignee', [
            '%old%' => $oldAssignee !== null ? (string)$oldAssignee : $this->translator->trans('problems.assignee.none'),
            '%new%' => $newAssignee !== null ? (string)$newAssignee : $this->translator->trans('problems.assignee.none')
        ]);
    }
}