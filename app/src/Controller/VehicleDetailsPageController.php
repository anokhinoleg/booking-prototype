<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\VehicleRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VehicleDetailsPageController extends AbstractController
{
    public function __construct(private VehicleRepositoryInterface $vehicleRepository)
    {
    }

    #[Route('/vehicle/{vehicleId}/rental-tab')]
    public function rentalTab(): Response
    {
        return $this->render('vehicle/details/rental-tab.html.twig',
        [
            'vehicles' => $this->vehicleRepository->findAll(),
        ]);
    }
}
