<?php

namespace App\Controller\Admin\AnnouncementCategory;

use App\Entity\AnnouncementCategory;
use App\Repository\AnnouncementCategoryRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RemoveAction extends AbstractController {

    public function __construct(private readonly AnnouncementCategoryRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/announcements/categories/{uuid}/remove', name: 'remove_announcementcategory')]
    public function remove(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] AnnouncementCategory $category): RedirectResponse|Response {
        if($category->getAnnouncements()->count() > 0) {
            $this->addFlash('error', 'announcements.categories.remove.error');
            return $this->redirectToRoute('admin_announcementcategories');
        }

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'announcements.categories.remove.confirm',
            'message_parameters' => [
                '%name%' => $category->getName()
            ]
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($category);

            $this->addFlash('success', 'announcements.categories.remove.success');
            return $this->redirectToRoute('admin_announcementcategories');
        }

        return $this->render('admin/announcements/categories/remove.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }
}