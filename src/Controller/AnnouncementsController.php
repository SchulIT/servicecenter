<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Repository\AnnouncementCategoryRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AnnouncementsController extends AbstractController {

    private $datehelper;

    public function __construct(DateHelper $dateHelper) {
        $this->datehelper = $dateHelper;
    }

    /**
     * @Route("/announcements", name="announcements")
     */
    public function index(AnnouncementCategoryRepositoryInterface $announcementCategoryRepository) {
        $categories = $announcementCategoryRepository
            ->findAllWithCurrentAnnouncements($this->datehelper->getToday());

        return $this->render('announcements/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/announcements/{uuid}", name="show_announcement")
     * @ParamConverter()
     */
    public function show(Announcement $announcement) {
        return $this->render('announcements/show.html.twig', [
            'announcement' => $announcement
        ]);
    }
}