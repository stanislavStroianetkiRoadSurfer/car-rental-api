<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Car;
use App\Repository\BookingRepository;
use App\Repository\CarRepository;
use App\Request\AvailabilityRequest;
use App\Response\AvailabilityResponse;
use App\Service\PricingApi\PricingClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class AvailableCarService
{
    public function __construct(
        private readonly CarRepository $carRepository,
        private readonly BookingRepository $bookingRepository,
        private readonly PricingClientInterface $pricingClient,
        private readonly CacheInterface $cache,
    ) {
    }

    public function getAvailableCars(
        AvailabilityRequest $request,
    ): array {
        return $this->cache->get(
            $this->getCacheKey($request),
            function (ItemInterface $item) use ($request): array {
                $item->expiresAfter(300); // Cache for 5 minutes

                return $this->getAvailableCarsInternal($request->stationId, $request->getStartDate(), $request->getEndDate());
            }
        );
    }

    private function getCacheKey(AvailabilityRequest $request): string
    {
        return md5(
            \sprintf(
                '%s_%s_%s_%s',
                self::class,
                $request->stationId,
                $request->startDate,
                $request->endDate
            )
        );
    }

    private function getAvailableCarsInternal(int $stationId, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $cars = $this->carRepository->getByStationId($stationId);

        $availableCars = array_filter(
            $cars,
            fn (Car $car): bool => 0 === \count($this->bookingRepository->getBookingsForCarInPeriod($car, $startDate, $endDate))
        );

        $prices = [];
        foreach ($availableCars as $car) {
            $price = $this->pricingClient->calculatePrice(
                $car->getId(),
                $startDate,
                $endDate,
            );

            $prices[$car->getId()] = $price;
        }

        return array_map(
            fn (Car $car): \App\Response\AvailabilityResponse => new AvailabilityResponse(
                $car->getId(),
                $car->getModel(),
                $car->getStation()?->getName(),
                $prices[$car->getId()] ?? 0
            ),
            $availableCars
        );
    }
}
