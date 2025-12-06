<?php

namespace App\Controller\Admin\ProblemTypes;

use App\Repository\DeviceTypeRepositoryInterface;
use App\Repository\PaginationQuery;
use App\Repository\ProblemTypeRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class IndexAction extends AbstractController {
    public function __construct(private readonly ProblemTypeRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/problemtypes', name: 'admin_problemtypes')]
    public function __invoke(DeviceTypeRepositoryInterface $deviceTypeRepository, #[MapQueryParameter] int $page = 1, #[MapQueryParameter(name: 'dt')] string|null $deviceTypeUuid = null): Response {
        $deviceType = null;

        if($deviceTypeUuid !== null) {
            $deviceType = $deviceTypeRepository->findOneByUuid($deviceTypeUuid);
        }

        return $this->render('admin/problemtypes/index.html.twig', [
            'types' => $this->repository->findAllPaginated(new PaginationQuery(page: $page), deviceType: $deviceType),
            'deviceType' => $deviceType,
            'deviceTypes' => $deviceTypeRepository->findAll()
        ]);
    }
}