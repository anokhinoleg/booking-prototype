<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Enum\ReservationStatuses;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/v1')]
#[OA\Tag(name: 'admin')]
final class DeclineReservationController extends AbstractController
{
    #[Route('/reservations/{id}/decline', methods: ['PUT'])]
    #[OA\Post(
        path: '/v1/reservations/{id}/decline',
    )]
    public function decline(int $id): JsonResponse
    {
        return $this->json([
            'id' => $id,
            'status' => ReservationStatuses::DECLINED->value,
        ]);
    }
}
