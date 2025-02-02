<?php

namespace App\Security;

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

    public function loadUserByIdentifier(string $identifier): UserInterface {
        if($identifier !== $this->username) {
            throw new UserNotFoundException();
        }

        return new InMemoryUser($this->username, $this->password, [ 'ROLE_CRON' ]);
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user): UserInterface {
        return $this->loadUserByUsername($this->username);
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class): bool {
        return $class === InMemoryUser::class;
    }
}