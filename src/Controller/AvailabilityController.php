<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request\AvailabilityRequest;
use App\Service\AvailabilityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AvailabilityController extends AbstractController
{  
    public function __construct(
        private readonly AvailabilityService $availablityService,
        private readonly ValidatorInterface $validator,
    ) {}
    
    #[Route('/availabilities', methods: [Request::METHOD_GET])]
    public function index(
        #[MapQueryParameter(name: 'station_id')]
        int $stationId,
        #[MapQueryParameter(name: 'from')]
        string $fromDateString,
        #[MapQueryParameter(name: 'to')]
        string $toDateString,
    ): JsonResponse 
    {
        $availabilityRequestDTO = new AvailabilityRequest($stationId, $fromDateString, $toDateString);
        
        $violations = $this->validator->validate($availabilityRequestDTO);
        if (\count($violations) > 0) {
            return new JsonResponse(['error' => (string) $violations], Response::HTTP_BAD_REQUEST);
        }

        $availableCars = $this->availablityService->getAvailableCarsWithPrices($availabilityRequestDTO);

        return new JsonResponse($availableCars, Response::HTTP_OK);
    }
}
