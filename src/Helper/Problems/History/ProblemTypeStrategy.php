<?php

namespace App\Helper\Problems\History;

use App\Entity\User;
use App\Repository\ProblemTypeRepositoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProblemTypeStrategy implements PropertyValueStrategyInterface {

    use UserDisplayNameTrait;

    public function __construct(private readonly TranslatorInterface $translator, private readonly ProblemTypeRepositoryInterface $problemTypeRepository)
    {
    }

    public function supportsProperty(string $name): bool {
        return $name === 'problemType';
    }

    public function getValue($value) {
        return $this->problemTypeRepository->findOneById($value['id']);
    }

    public function getText(?User $user, string $username, $value): string {
        return $this->translator->trans('problems.history.problemtype', [
            '%user%' => $this->getUserDisplayName($user, $username),
            '%type%' => $this->getValue($value)->getName()
        ]);
    }
}