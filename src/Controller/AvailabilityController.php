<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request\AvailabilityRequest;
use App\Service\AvailableCarService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AvailabilityController extends AbstractController
{
    #[Route('/availabilities', methods: [Request::METHOD_GET])]
    public function index(
        #[MapQueryParameter(name: 'station_id')]
        int $stationId,
        #[MapQueryParameter(name: 'from')]
        string $fromDateString,
        #[MapQueryParameter(name: 'to')]
        string $toDateString,
        AvailableCarService $availableCarService,
        ValidatorInterface $validator,
    ): JsonResponse {
        $request = new AvailabilityRequest(
            stationId: $stationId,
            startDate: $fromDateString,
            endDate: $toDateString
        );

        $violations = $validator->validate($request);
        if (\count($violations) > 0) {
            return new JsonResponse(['error' => 'Invalid request'], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($availableCarService->getAvailableCars($request), Response::HTTP_OK);
    }
}
