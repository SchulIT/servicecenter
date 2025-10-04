<?php

declare(strict_types=1);

namespace App\Helper\Problems\History;

use Override;
use App\Entity\ProblemType;
use App\Entity\User;
use App\Repository\ProblemTypeRepositoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProblemTypeStrategy implements PropertyValueStrategyInterface {

    use UserDisplayNameTrait;

    public function __construct(private readonly TranslatorInterface $translator, private readonly ProblemTypeRepositoryInterface $problemTypeRepository)
    {
    }

    #[Override]
    public function supportsProperty(string $name): bool {
        return $name === 'problemType';
    }

    #[Override]
    public function getValue($value): ?ProblemType {
        return $this->problemTypeRepository->findOneById($value['id']);
    }

    #[Override]
    public function getText(?User $user, string $username, $value): string {
        return $this->translator->trans('problems.history.problemtype', [
            '%user%' => $this->getUserDisplayName($user, $username),
            '%type%' => $this->getValue($value)->getName()
        ]);
    }
}
