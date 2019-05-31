<?php

namespace App\Menu;

use App\Entity\Announcement;
use App\Entity\Problem;
use App\Repository\AnnouncementRepository;
use App\Repository\AnnouncementRepositoryInterface;
use App\Repository\ProblemRepository;
use App\Repository\ProblemRepositoryInterface;
use Knp\Menu\FactoryInterface;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Builder {

    private $factory;
    private $tokenStorage;
    private $announcementRepository;
    private $problemRepository;
    private $authorizationChecker;
    private $dateHelper;

    public function __construct(FactoryInterface $factory, TokenStorageInterface $tokenStorage,
                                ProblemRepositoryInterface $problemRepository,
                                AnnouncementRepositoryInterface $announcementRepository,
                                AuthorizationCheckerInterface $authorizationChecker, DateHelper $dateHelper) {
        $this->factory = $factory;
        $this->tokenStorage = $tokenStorage;
        $this->announcementRepository = $announcementRepository;
        $this->problemRepository = $problemRepository;
        $this->authorizationChecker = $authorizationChecker;
        $this->dateHelper = $dateHelper;
    }

    public function mainMenu(array $options) {
        $user = $this->tokenStorage
            ->getToken()->getUser();

        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'nav nav-pills flex-column');

        $menu->addChild('menu.label', [
            'attributes' => [
                'class' => 'header'
            ]
        ]);

        $menu->addChild('dashboard.label', [
            'route' => 'dashboard'
        ]);

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $count = $this->problemRepository->countOpen();
        } else {
            $count = $this->problemRepository->countByUser($user);
        }

        $menu->addChild('problems.label', [
            'route' => 'problems'
        ])
            ->setAttribute('count', $count);

        $menu->addChild('status.label', [
            'route' => 'current_status'
        ]);

        $menu->addChild('announcements.label', [
            'route' => 'announcements'
        ])
            ->setAttribute('count', $this->announcementRepository->countActive($this->dateHelper->getToday()));

        $menu->addChild('wiki.label', [
            'route' => 'wiki'
        ]);

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu->addChild('administration.label', [
                'attributes' => [
                    'class' => 'header'
                ]
            ]);

            $menu->addChild('devices.label', [
                'route' => 'devices'
            ]);
            $menu->addChild('statistics.label', [
                'route' => 'statistics'
            ]);
            $menu->addChild('placards.label', [
                'route' => 'placards'
            ]);
            $menu->addChild('notifications.label', [
                'route' => 'notifications'
            ]);
        }

        if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $menu->addChild('admin_announcements', [
                'route' => 'admin_announcements',
                'label' => 'announcements.label'
            ]);
            $menu->addChild('device_types.label', [
                'route' => 'admin_devicetypes'
            ]);
            $menu->addChild('rooms.label', [
                'route' => 'admin_rooms'
            ]);
            $menu->addChild('problem_types.label', [
                'route' => 'admin_problemtypes'
            ]);
            $menu->addChild('logs.label', [
                'route' => 'admin_logs'
            ]);
            $menu->addChild('mails.label', [
                'route' => 'admin_mails'
            ]);
        }

        return $menu;
    }

}