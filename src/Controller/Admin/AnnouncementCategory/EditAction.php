<?php

namespace App\Controller\Admin\AnnouncementCategory;

use App\Entity\AnnouncementCategory;
use App\Form\AnnouncementCategoryType;
use App\Repository\AnnouncementCategoryRepositoryInterface;
use SchulIT\CommonBundle\Http\Attribute\ForbiddenRedirect;
use SchulIT\CommonBundle\Http\Attribute\NotFoundRedirect;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditAction extends AbstractController {
    public function __construct(private readonly AnnouncementCategoryRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/announcements/categories/{uuid}/edit', name: 'edit_announcementcategory')]
    #[NotFoundRedirect(redirectRoute: 'admin_announcementcategories', flashMessage: 'announcements.categories.not_found')]
    #[ForbiddenRedirect(redirectRoute: 'admin_announcementcategories', flashMessage: 'announcements.categories.not_found')]
    public function __invoke(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] AnnouncementCategory $category): RedirectResponse|Response {
        $form = $this->createForm(AnnouncementCategoryType::class, $category, [ ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($category);

            $this->addFlash('success', 'announcements.categories.edit.success');
            return $this->redirectToRoute('admin_announcementcategories');
        }

        return $this->render('admin/announcements/categories/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }
}