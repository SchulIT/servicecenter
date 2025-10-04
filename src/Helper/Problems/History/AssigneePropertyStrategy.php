<?php

declare(strict_types=1);

namespace App\Helper\Problems\History;

use Override;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AssigneePropertyStrategy implements PropertyValueStrategyInterface {

    use UserDisplayNameTrait;

    public function __construct(private readonly UserRepositoryInterface $userRepository, private TranslatorInterface $translator)
    {
    }

    #[Override]
    public function supportsProperty(string $name): bool {
        return $name === 'assignee';
    }

    #[Override]
    public function getValue($value): ?User {
        if($value === null) {
            return null;
        }

        return $this->userRepository->findOneById($value['id']);
    }

    #[Override]
    public function getText(?User $user, string $username, $value): string {
        $messageId = 'problems.history.assignee.none';

        if($value !== null) {
            $messageId = 'problems.history.assignee.taken';
        }

        return $this->translator->trans($messageId, [
            '%user%' => $this->getUserDisplayName($user, $username),
        ]);
    }
}
