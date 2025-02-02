<?php

namespace App\Controller;

use App\Entity\NotificationSetting;
use App\Form\NotificationSettingType;
use App\Repository\NotificationSettingRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class NotificationsController extends AbstractController {
    #[Route(path: '/notifications', name: 'notifications')]
    public function index(#[CurrentUser] $user, Request $request, NotificationSettingRepositoryInterface $notificationSettingRepository): RedirectResponse|Response {
        $settings = $notificationSettingRepository
            ->findOneByUser($user);

        if($settings === null) {
            $settings = (new NotificationSetting())
                ->setUser($user);
        }

        $form = $this->createForm(NotificationSettingType::class, $settings, [ ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $notificationSettingRepository->persist($settings);

            $this->addFlash('success', 'notifications.success');
            return $this->redirectToRoute('notifications');
        }

        return $this->render('notifications/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}