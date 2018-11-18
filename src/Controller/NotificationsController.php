<?php

namespace App\Controller;

use App\Entity\NotificationSetting;
use App\Form\NotificationSettingType;
use App\Repository\NotificationSettingRepositoryInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class NotificationsController extends Controller {
    /**
     * @Route("/notifications", name="notifications")
     */
    public function index(Request $request, NotificationSettingRepositoryInterface $notificationSettingRepository) {
        $em = $this->getDoctrine()->getManager();

        $settings = $notificationSettingRepository
            ->findOneByUser($this->getUser());

        if($settings === null) {
            $settings = (new NotificationSetting())
                ->setUser($this->getUser());
        }

        $form = $this->createForm(NotificationSettingType::class, $settings, [ ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($settings);
            $em->flush();

            $this->addFlash('success', 'notifications.success');
            return $this->redirectToRoute('notifications');
        }

        return $this->render('notifications/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}