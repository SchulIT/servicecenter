<?php

namespace App\Helper\Problems\History;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AssigneePropertyStrategy implements PropertyValueStrategyInterface {

    private $userRepository;
    private $translator;

    public function __construct(UserRepositoryInterface $userRepository, TranslatorInterface $translator) {
        $this->userRepository = $userRepository;
        $this->translator = $translator;
    }

    public function supportsProperty(string $name): bool {
        return $name === 'assignee';
    }

    public function getValue($value) {
        if($value === null) {
            return null;
        }

        return $this->userRepository->findOneById($value['id']);
    }

    public function getText(User $user, $value): string {
        $messageId = 'problems.history.assignee.none';

        if($value !== null) {
            $messageId = 'problems.history.assignee.taken';
        }

        return $this->translator->trans($messageId, [
            '%user%' => $user,
        ]);
    }
}