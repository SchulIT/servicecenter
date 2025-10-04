<?php

declare(strict_types=1);

namespace App\Security;

use Override;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

readonly class CronUserProvider implements UserProviderInterface {

    public function __construct(private string $username, private string $password)
    {
    }

    public function loadUserByUsername(string $username): UserInterface {
        return $this->loadUserByIdentifier($username);
    }

    #[Override]
    public function loadUserByIdentifier(string $identifier): UserInterface {
        if($identifier !== $this->username) {
            throw new UserNotFoundException();
        }

        return new InMemoryUser($this->username, $this->password, [ 'ROLE_CRON' ]);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function refreshUser(UserInterface $user): UserInterface {
        return $this->loadUserByUsername($this->username);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function supportsClass($class): bool {
        return $class === InMemoryUser::class;
    }
}
