<?php

declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class AdminMenuBuilder extends AbstractMenuBuilder {

    public function __construct(FactoryInterface $factory, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker, TranslatorInterface $translator) {
        parent::__construct($factory, $tokenStorage, $authorizationChecker, $translator);
    }

    public function adminMenu(array $options): ItemInterface {
        $root = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'navbar-nav float-lg-right'
            ]);

        $menu = $root->addChild('admin', [
            'label' => ''
        ])
            ->setExtra('icon', 'fa fa-cogs')
            ->setAttribute('title', $this->translator->trans('administration.label'))
            ->setExtra('menu', 'admin')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true);

        if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $menu->addChild('settings.label', [
                'route' => 'settings'
            ])
                ->setExtra('icon', 'fa fa-cogs');
        }

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu->addChild('devices.label', [
                'route' => 'devices'
            ])
                ->setExtra('icon', 'fas fa-desktop');
            $menu->addChild('statistics.label', [
                'route' => 'statistics'
            ])
                ->setExtra('icon', 'fas fa-chart-pie');
        }

        if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $menu->addChild('admin_announcements', [
                'route' => 'admin_announcements',
                'label' => 'announcements.label'
            ])
                ->setExtra('icon', 'fas fa-bullhorn');
            $menu->addChild('device_types.label', [
                'route' => 'admin_devicetypes'
            ])
                ->setExtra('icon', 'fas fa-tools');
            $menu->addChild('rooms.label', [
                'route' => 'admin_rooms'
            ])
                ->setExtra('icon', 'fas fa-door-open');
            $menu->addChild('problem_types.label', [
                'route' => 'admin_problemtypes'
            ])
                ->setExtra('icon', 'fas fa-tools');

            $menu->addChild('applications.label', [
                'route' => 'applications'
            ])
                ->setExtra('icon', 'fas fa-key');

            $menu->addChild('messenger.label', [
                'route' => 'zenstruck_messenger_monitor_dashboard'
            ])
                ->setExtra('icon', 'fas fa-envelope-open-text')
                ->setLinkAttribute('target', '_blank');

            $menu->addChild('logs.label', [
                'route' => 'admin_logs'
            ])
                ->setExtra('icon', 'fas fa-clipboard-list');
        }

        return $root;
    }
}
