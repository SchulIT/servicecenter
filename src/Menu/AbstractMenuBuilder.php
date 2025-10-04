<?php

declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly abstract class AbstractMenuBuilder {
    public function __construct(protected FactoryInterface $factory,
                                protected TokenStorageInterface $tokenStorage,
                                protected AuthorizationCheckerInterface $authorizationChecker,
                                protected TranslatorInterface $translator) { }
}
