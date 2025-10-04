<?php

declare(strict_types=1);

namespace App\Controller;

use App\Settings\AppSettings;
use Jbtronics\SettingsBundle\Form\SettingsFormBuilderInterface;
use Jbtronics\SettingsBundle\Form\SettingsFormFactoryInterface;
use Jbtronics\SettingsBundle\Manager\SettingsManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SUPER_ADMIN')]
class SettingsController extends AbstractController {
    #[Route(path: '/admin/settings', name: 'settings')]
    public function app(Request $request, SettingsManagerInterface $settingsManager, SettingsFormFactoryInterface $settingsFormFactory): Response {
        $settings = [
            $settingsManager->createTemporaryCopy(AppSettings::class)
        ];

        $form = $settingsFormFactory->createMultiSettingsFormBuilder($settings)->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            foreach ($settings as $setting) {
                $settingsManager->mergeTemporaryCopy($setting);
            }

            $settingsManager->save();
            $this->addFlash('success', 'settings.success');
            return $this->redirectToRoute('settings');
        }

        return $this->render('admin/settings/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
