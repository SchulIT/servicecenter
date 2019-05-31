<?php

namespace App\Helper\Problems\History;

use App\Entity\User;
use App\Repository\ProblemTypeRepositoryInterface;

class ProblemTypeStrategy implements PropertyValueStrategyInterface {

    private $problemTypeRepository;

    public function __construct(ProblemTypeRepositoryInterface $problemTypeRepository) {
        $this->problemTypeRepository = $problemTypeRepository;
    }

    public function supportsProperty(string $name): bool {
        return $name === 'problemType';
    }

    public function getValue($value) {
        return $this->problemTypeRepository->findOneById($value['id']);
    }

    public function getText(User $user, $value): string {

    }
}