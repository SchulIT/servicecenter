<?php

namespace App\Controller\Admin\AnnouncementCategory;

use App\Entity\AnnouncementCategory;
use App\Form\AnnouncementCategoryType;
use App\Repository\AnnouncementCategoryRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AddAction extends AbstractController {
    public function __construct(private readonly AnnouncementCategoryRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/announcements/categories/add', name: 'add_announcementcategory')]
    public function __invoke(Request $request): RedirectResponse|Response {
        $category = new AnnouncementCategory();
        $form = $this->createForm(AnnouncementCategoryType::class, $category, [ ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($category);

            $this->addFlash('success', 'announcements.categories.add.success');
            return $this->redirectToRoute('admin_announcementcategories');
        }

        return $this->render('admin/announcements/categories/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}