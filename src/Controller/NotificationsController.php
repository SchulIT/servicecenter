<?php

namespace App\Controller;

use App\Entity\NotificationSetting;
use App\Entity\User;
use App\Form\NotificationSettingType;
use App\Repository\NotificationSettingRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Security("is_granted('ROLE_ADMIN')")]
class NotificationsController extends AbstractController {
    #[Route(path: '/notifications', name: 'notifications')]
    public function index(Request $request, NotificationSettingRepositoryInterface $notificationSettingRepository) {
        /** @var User $user */
        $user = $this->getUser();

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