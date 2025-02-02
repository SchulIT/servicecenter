<?php

namespace App\Menu;

use App\Repository\AnnouncementRepositoryInterface;
use App\Repository\ProblemRepositoryInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use SchulIT\CommonBundle\Helper\DateHelper;

readonly class Builder {

    public function __construct(private FactoryInterface $factory, private ProblemRepositoryInterface $problemRepository, private AnnouncementRepositoryInterface $announcementRepository, private DateHelper $dateHelper)
    {
    }

    public function mainMenu(array $options): ItemInterface {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'navbar-nav me-auto');

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
            ->setExtra('icon', 'fas fa-question-circle');

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
}
