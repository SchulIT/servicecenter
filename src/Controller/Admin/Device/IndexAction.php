<?php

namespace App\Controller\Admin\Device;

use App\Repository\DeviceRepositoryInterface;
use App\Repository\DeviceTypeRepositoryInterface;
use App\Repository\PaginationQuery;
use App\Repository\RoomCategoryRepositoryInterface;
use App\Repository\RoomRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class IndexAction extends AbstractController {
    #[Route(path: '/admin/devices', name: 'devices')]
    public function __invoke(
        DeviceRepositoryInterface $repository,
        RoomRepositoryInterface $roomRepository,
        DeviceTypeRepositoryInterface $deviceTypeRepository,
        RoomCategoryRepositoryInterface $roomCategoryRepository,
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter(name: 'dt')] string|null $deviceTypeUuid = null,
        #[MapQueryParameter(name: 'room')] string|null $roomUuid = null,
        #[MapQueryParameter] string|null $q = null
    ): Response {
        $room = null;
        $deviceType = null;

        if(!empty($deviceTypeUuid)) {
            $deviceType = $deviceTypeRepository->findOneByUuid($deviceTypeUuid);
        }

        if(!empty($roomUuid)) {
            $room = $roomRepository->findOneByUuid($roomUuid);
        }

        return $this->render('devices/index.html.twig', [
            'q' => $q,
            'room' => $room,
            'deviceType' => $deviceType,
            'deviceTypes' => $deviceTypeRepository->findAll(),
            'categories' => $roomCategoryRepository->findAll(),
            'devices' => $repository->findAllPaginated(
                new PaginationQuery(page: $page),
                room: $room,
                deviceType: $deviceType,
                query: $q
            )
        ]);
    }

}