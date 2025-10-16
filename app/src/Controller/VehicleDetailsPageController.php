<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VehicleDetailsPageController extends AbstractController
{
    #[Route('/vehicle/{vehicleId}/rental-tab')]
    public function rentalTab(): Response
    {
        return $this->render('vehicle/details/rental-tab.html.twig');
    }
}
