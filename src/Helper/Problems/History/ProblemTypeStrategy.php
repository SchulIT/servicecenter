<?php

namespace App\Helper\Problems\History;

use App\Entity\User;
use App\Repository\ProblemTypeRepositoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProblemTypeStrategy implements PropertyValueStrategyInterface {

    private $translator;
    private $problemTypeRepository;

    public function __construct(TranslatorInterface $translator, ProblemTypeRepositoryInterface $problemTypeRepository) {
        $this->translator = $translator;
        $this->problemTypeRepository = $problemTypeRepository;
    }

    public function supportsProperty(string $name): bool {
        return $name === 'problemType';
    }

    public function getValue($value) {
        return $this->problemTypeRepository->findOneById($value['id']);
    }

    public function getText(User $user, $value): string {
        return $this->translator->trans('problems.history.problemtype', [
            '%user%' => $user,
            '%type%' => $this->getValue($value)->getName()
        ]);
    }
}