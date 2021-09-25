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
            ->setExtra('icon', 'fa fa-home');

        $count = $this->problemRepository->countOpen();

        $menu->addChild('problems.label', [
            'route' => 'problems'
        ])
            ->setExtra('count', $count)
            ->setExtra('icon', 'fas fa-exclamation-circle');

        $menu->addChild('status.label', [
            'route' => 'current_status'
        ])
            ->setExtra('icon', 'far fa-question-circle');

        $menu->addChild('announcements.label', [
            'route' => 'announcements'
        ])
            ->setExtra('count', $this->announcementRepository->countActive($this->dateHelper->getToday()))
            ->setExtra('icon', 'fas fa-bullhorn');

        $menu->addChild('wiki.label', [
            'route' => 'wiki'
        ])
            ->setExtra('icon', 'fab fa-wikipedia-w');


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
            ->setExtra('icon', 'fa fa-cogs')
            ->setAttribute('title', $this->translator->trans('administration.label'))
            ->setExtra('menu', 'admin')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true);

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu->addChild('devices.label', [
                'route' => 'devices'
            ])
                ->setExtra('icon', 'fas fa-desktop');
            $menu->addChild('statistics.label', [
                'route' => 'statistics'
            ])
                ->setExtra('icon', 'fas fa-chart-pie');
            $menu->addChild('placards.label', [
                'route' => 'placards'
            ])
                ->setExtra('icon', 'far fa-list-alt');
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

            $menu->addChild('cron.label', [
                'route' => 'admin_cronjobs'
            ])
                ->setExtra('icon', 'fas fa-history');

            $menu->addChild('logs.label', [
                'route' => 'admin_logs'
            ])
                ->setExtra('icon', 'fas fa-clipboard-list');
            $menu->addChild('mails.label', [
                'route' => 'admin_mails'
            ])
                ->setExtra('icon', 'far fa-envelope');
            $menu->addChild('idp_exchange.label', [
                'route' => 'idp_exchange_admin'
            ])
                ->setExtra('icon', 'fas fa-exchange-alt');
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
            ->setExtra('icon', 'fa fa-user')
            ->setExtra('menu', 'user')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true);

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $userMenu->addChild('notifications.label', [
                'route' => 'notifications'
            ])
                ->setExtra('icon', 'far fa-bell');
        }

        $userMenu->addChild('profile.label', [
            'uri' => $this->idpProfileUrl
        ])
            ->setLinkAttribute('target', '_blank')
            ->setExtra('icon', 'far fa-address-card');

        $label = 'dark_mode.enable';
        $icon = 'far fa-moon';

        if($this->darkModeManager->isDarkModeEnabled()) {
            $label = 'dark_mode.disable';
            $icon = 'far fa-sun';
        }

        $userMenu->addChild($label, [
            'route' => 'toggle_darkmode'
        ])
            ->setExtra('icon', $icon);

        $menu->addChild('label.logout', [
            'route' => 'logout',
            'label' => ''
        ])
            ->setExtra('icon', 'fas fa-sign-out-alt')
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
                ->setExtra('icon', 'fa fa-th')
                ->setExtra('menu', 'services')
                ->setExtra('pull-right', true)
                ->setAttribute('title', $this->translator->trans('services.label'));

            foreach($token->getAttribute('services') as $service) {
                $item = $menu->addChild($service->name, [
                    'uri' => $service->url
                ])
                    ->setAttribute('title', $service->description)
                    ->setLinkAttribute('target', '_blank');

                if(isset($service->icon) && !empty($service->icon)) {
                    $item->setExtra('icon', $service->icon);
                }
            }
        }

        return $root;
    }
}