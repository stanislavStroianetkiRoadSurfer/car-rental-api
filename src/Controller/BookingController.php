<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request\BookingCreateRequest;
use App\Service\BookingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/bookings')]
class BookingController extends AbstractController
{
    #[Route('', name: 'booking_create', methods: [Request::METHOD_POST])]
    public function create(
        Request $request,
        ValidatorInterface $validator,
        BookingService $bookingService,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $bookingCreateRequestDTO = new BookingCreateRequest(
            $data['car_id'] ?? null,
            $data['email'] ?? null,
        );
        $violations = $validator->validate($bookingCreateRequestDTO);
        if (\count($violations) > 0) {
            return new JsonResponse(['error' => (string) $violations], Response::HTTP_BAD_REQUEST);
        }

        $booking = $bookingService->create($bookingCreateRequestDTO);

        return $this->json(
            [
                'success' => true,
                'booking_id' => $booking->getId(),
            ],
            Response::HTTP_CREATED
        );
    }
}
