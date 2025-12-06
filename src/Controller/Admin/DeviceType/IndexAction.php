<?php

namespace App\Controller\Admin\DeviceType;

use App\Repository\DeviceTypeRepositoryInterface;
use App\Repository\PaginationQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class IndexAction extends AbstractController {
    public function __construct(private readonly DeviceTypeRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/devicetypes', name: 'admin_devicetypes')]
    public function index(#[MapQueryParameter] int $page = 1): Response {
        return $this->render('admin/devicetypes/index.html.twig', [
            'types' => $this->repository->findAllPaginated(new PaginationQuery($page))
        ]);
    }
}