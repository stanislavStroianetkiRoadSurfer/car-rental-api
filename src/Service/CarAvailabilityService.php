<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Car;
use App\Repository\CarRepository;
use App\Request\AvailabilityRequest;
use App\ViewModel\AvailabilityViewModel;

class CarAvailabilityService
{
    public function __construct(
        private readonly CarRepository $carRepository,
    ) {
    }

    public function getAvailableCars(
        AvailabilityRequest $request,
    ): array {
        $availableCars = $this->carRepository->getAvailableCars(
            $request->getStationId(),
            $request->getStartDate(),
            $request->getEndDate(),
        );

        // Todo: fetch and append proces, calculate and append premium flag

        return array_map(
            fn (Car $car): AvailabilityViewModel => new AvailabilityViewModel(
                $car->getId(),
                $car->getModel(),
                $request->getStationId(),
                0.0,
                false,
            ),
            $availableCars
        );
    }
}