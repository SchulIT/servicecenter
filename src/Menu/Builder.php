<?php

namespace App\Menu;

use App\Entity\Announcement;
use App\Entity\Problem;
use App\Entity\User;
use App\Repository\AnnouncementRepository;
use App\Repository\AnnouncementRepositoryInterface;
use App\Repository\ProblemRepository;
use App\Repository\ProblemRepositoryInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;
use SchulIT\CommonBundle\DarkMode\DarkModeManagerInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Builder {

    private $factory;
    private $tokenStorage;
    private $announcementRepository;
    private $problemRepository;
    private $authorizationChecker;
    private $dateHelper;
    private $translator;
    private $darkModeManager;

    private $idpProfileUrl;

    public function __construct(FactoryInterface $factory, TokenStorageInterface $tokenStorage,
                                ProblemRepositoryInterface $problemRepository,
                                AnnouncementRepositoryInterface $announcementRepository,
                                AuthorizationCheckerInterface $authorizationChecker, DateHelper $dateHelper,
                                TranslatorInterface $translator, DarkModeManagerInterface $darkModeManager, string $idpProfileUrl) {
        $this->factory = $factory;
        $this->tokenStorage = $tokenStorage;
        $this->announcementRepository = $announcementRepository;
        $this->problemRepository = $problemRepository;
        $this->authorizationChecker = $authorizationChecker;
        $this->dateHelper = $dateHelper;
        $this->translator = $translator;
        $this->darkModeManager = $darkModeManager;
        $this->idpProfileUrl = $idpProfileUrl;
    }

    public function mainMenu(array $options): ItemInterface {
        $user = $this->tokenStorage
            ->getToken()->getUser();

        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'navbar-nav mr-auto');

        $menu->addChild('dashboard.label', [
            'route' => 'dashboard'
        ])
            ->setAttribute('icon', 'fa fa-home');

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $count = $this->problemRepository->countOpen();
        } else {
            $count = $this->problemRepository->countByUser($user);
        }

        $menu->addChild('problems.label', [
            'route' => 'problems'
        ])
            ->setAttribute('count', $count)
            ->setAttribute('icon', 'fas fa-exclamation-circle');

        $menu->addChild('status.label', [
            'route' => 'current_status'
        ])
            ->setAttribute('icon', 'far fa-question-circle');

        $menu->addChild('announcements.label', [
            'route' => 'announcements'
        ])
            ->setAttribute('count', $this->announcementRepository->countActive($this->dateHelper->getToday()))
            ->setAttribute('icon', 'fas fa-bullhorn');

        $menu->addChild('wiki.label', [
            'route' => 'wiki'
        ])
            ->setAttribute('icon', 'fab fa-wikipedia-w');


        return $menu;
    }

    public function adminMenu(array $options): ItemInterface {
        $root = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'navbar-nav float-lg-right'
            ]);

        $menu = $root->addChild('admin', [
            'label' => ''
        ])
            ->setAttribute('icon', 'fa fa-cogs')
            ->setAttribute('title', $this->translator->trans('admin.label'))
            ->setExtra('menu', 'admin')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true);

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu->addChild('devices.label', [
                'route' => 'devices'
            ])
                ->setAttribute('icon', 'fas fa-desktop');
            $menu->addChild('statistics.label', [
                'route' => 'statistics'
            ])
                ->setAttribute('icon', 'fas fa-chart-pie');
            $menu->addChild('placards.label', [
                'route' => 'placards'
            ])
                ->setAttribute('icon', 'far fa-list-alt');
            $menu->addChild('notifications.label', [
                'route' => 'notifications'
            ])
                ->setAttribute('icon', 'far fa-bell');
        }

        if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $menu->addChild('admin_announcements', [
                'route' => 'admin_announcements',
                'label' => 'announcements.label'
            ])
                ->setAttribute('icon', 'fas fa-bullhorn');
            $menu->addChild('device_types.label', [
                'route' => 'admin_devicetypes'
            ])
                ->setAttribute('icon', 'fas fa-tools');
            $menu->addChild('rooms.label', [
                'route' => 'admin_rooms'
            ])
                ->setAttribute('icon', 'fas fa-door-open');
            $menu->addChild('problem_types.label', [
                'route' => 'admin_problemtypes'
            ])
                ->setAttribute('icon', 'fas fa-tools');

            $menu->addChild('cron.label', [
                'route' => 'admin_cronjobs'
            ])
                ->setAttribute('icon', 'fas fa-history');

            $menu->addChild('logs.label', [
                'route' => 'admin_logs'
            ])
                ->setAttribute('icon', 'fas fa-clipboard-list');
            $menu->addChild('mails.label', [
                'route' => 'admin_mails'
            ])
                ->setAttribute('icon', 'far fa-envelope');
            $menu->addChild('idp_exchange.label', [
                'route' => 'idp_exchange_admin'
            ])
                ->setAttribute('icon', 'fas fa-exchange-alt');
        }


        return $root;
    }

    public function userMenu(array $options): ItemInterface {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'navbar-nav float-lg-right'
            ]);

        $user = $this->tokenStorage->getToken()->getUser();

        if($user === null || !$user instanceof User) {
            return $menu;
        }

        $displayName = $user->getUsername();

        $userMenu = $menu->addChild('user', [
            'label' => $displayName
        ])
            ->setAttribute('icon', 'fa fa-user')
            ->setExtra('menu', 'user')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true);

        $userMenu->addChild('profile.label', [
            'uri' => $this->idpProfileUrl
        ])
            ->setAttribute('target', '_blank')
            ->setAttribute('icon', 'far fa-address-card');

        $label = 'dark_mode.enable';
        $icon = 'far fa-moon';

        if($this->darkModeManager->isDarkModeEnabled()) {
            $label = 'dark_mode.disable';
            $icon = 'far fa-sun';
        }

        $userMenu->addChild($label, [
            'route' => 'toggle_darkmode'
        ])
            ->setAttribute('icon', $icon);

        $menu->addChild('label.logout', [
            'route' => 'logout',
            'label' => ''
        ])
            ->setAttribute('icon', 'fas fa-sign-out-alt')
            ->setAttribute('title', $this->translator->trans('auth.logout'));

        return $menu;
    }

    public function servicesMenu(): ItemInterface {
        $root = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'navbar-nav float-lg-right'
            ]);

        $token = $this->tokenStorage->getToken();

        if($token instanceof SamlSpToken) {
            $menu = $root->addChild('services', [
                'label' => ''
            ])
                ->setAttribute('icon', 'fa fa-th')
                ->setExtra('menu', 'services')
                ->setExtra('pull-right', true)
                ->setAttribute('title', $this->translator->trans('services.label'));

            foreach($token->getAttribute('services') as $service) {
                $menu->addChild($service->name, [
                    'uri' => $service->url
                ])
                    ->setAttribute('title', $service->description)
                    ->setAttribute('target', '_blank');
            }
        }

        return $root;
    }
}