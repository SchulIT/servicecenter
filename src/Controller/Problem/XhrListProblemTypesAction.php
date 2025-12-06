<?php

namespace App\Controller\Problem;

use App\Repository\DeviceRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class XhrListProblemTypesAction extends AbstractController {
    #[Route(path: '/problems/xhr/problem_types', name: 'problem_ajax')]
    public function __invoke(
        #[MapQueryParameter(name: 'device')] int $deviceId,
        DeviceRepositoryInterface $deviceRepository,
        TranslatorInterface $translator
    ): JsonResponse {
        $device = $deviceRepository
            ->findOneById($deviceId);

        if($device === null) {
            throw new NotFoundHttpException();
        }

        $types = $device->getType()->getProblemTypes();

        $result = [ ];

        $result[] = [
            'value' => '',
            'placeholder' => true,
            'label' => $translator->trans('label.choose.problemtype')
        ];

        foreach($types as $type) {
            $result[] = [
                'value' => $type->getId(),
                'label' => $type->getName()
            ];
        }

        return new JsonResponse($result);
    }
}