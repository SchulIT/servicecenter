<?php

namespace App\Helper\Problems;

use App\Entity\Problem;
use Doctrine\ORM\EntityManagerInterface;

class BulkActionManager {

    /** @var AbstractBulkAction[] */
    private $actions;

    private $em;

    public function __construct(MarkSolvedAction $markSolvedAction, SetContactPersonAction $setContactPersonAction,
                                SetMaintenanceAction $setMaintenanceAction, UnsetMaintenanceAction $unsetMaintenanceAction,
                                EntityManagerInterface $entityManager) {
        $this->addAction($markSolvedAction);
        $this->addAction($setContactPersonAction);
        $this->addAction($setMaintenanceAction);
        $this->addAction($unsetMaintenanceAction);

        $this->em = $entityManager;
    }

    private function addAction(BulkActionInterface $action) {
        $this->actions[$action->getName()] = $action;
    }

    /**
     * @param Problem[] $problems
     * @param $action
     */
    public function run(array $problems, string $action) {
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