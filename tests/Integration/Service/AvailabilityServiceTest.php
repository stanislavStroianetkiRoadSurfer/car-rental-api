<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service;

use App\Entity\Station;
use App\Repository\StationRepository;
use App\Request\AvailabilityRequest;
use App\Service\AvailabilityService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AvailabilityServiceTest extends KernelTestCase
{
    private AvailabilityService $availabilityService;
    private StationRepository $stationRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->availabilityService = $container->get(AvailabilityService::class);
        $this->stationRepository = $container->get(StationRepository::class);
    }

    public function test_no_available_cars_for_station_without_active_cars(): void
    {
        // Given
        $station = $this->getStationForName('Berlin');

        // When
        $request = $this->createRequest(
            $station->getId(),
            '2026-05-20',
            '2026-05-21',
        );
        $availableCars = $this->availabilityService->getAvailableCarsWithPrices($request);

        // Then
        $this->assertEmpty($availableCars);
    }

    private function createRequest(int $stationId, string $fromString, string $toString): \App\Request\AvailabilityRequest
    {
        return new AvailabilityRequest(
            $stationId,
            $fromString,
            $toString,
        );
    }

    private function getStationForName(string $stationName): Station
    {
        return $this->stationRepository->findOneBy(['name' => $stationName]);
    }
}
