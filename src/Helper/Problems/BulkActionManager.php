<?php

namespace App\Helper\Problems;

use App\Entity\Problem;
use Doctrine\ORM\EntityManagerInterface;

class BulkActionManager {

    /** @var BulkActionInterface[] */
    private ?array $actions = null;

    public function __construct(MarkSolvedAction $markSolvedAction, SetAssigneeAction $setAssigneeAction,
                                SetMaintenanceAction $setMaintenanceAction, UnsetMaintenanceAction $unsetMaintenanceAction,
                                private readonly EntityManagerInterface $em) {
        $this->addAction($markSolvedAction);
        $this->addAction($setAssigneeAction);
        $this->addAction($setMaintenanceAction);
        $this->addAction($unsetMaintenanceAction);
    }

    private function addAction(BulkActionInterface $action): void {
        $this->actions[$action->getName()] = $action;
    }

    /**
     * @param Problem[] $problems
     */
    public function run(array $problems, string $action): void {
        $actionObject = $this->actions[$action];

        foreach($problems as $problem) {
            if($actionObject->performAction($problem) === true) {
                $this->em->persist($problem);
            }
        }

        $this->em->flush();
    }

    public function canRunAction(string $action): bool {
        return isset($this->actions[$action]) === true;
    }
}