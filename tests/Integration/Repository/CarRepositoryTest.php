<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\Entity\Station;
use App\Repository\CarRepository;
use App\Repository\StationRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CarRepositoryTest extends KernelTestCase
{
    private CarRepository $carRepository;

    private StationRepository $stationRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->carRepository = $container->get(CarRepository::class);
        $this->stationRepository = $container->get(StationRepository::class);
    }

    public function test_returns_no_cars_for_station_without_active_cars(): void
    {
        // Given
        $station = $this->stationRepository->findOneBy(['name' => 'Berlin']);
        
        // When
        $availableCars = $this->carRepository->getAllActiveCarsOfStation($station->getId());

        // Then
        $this->assertEmpty($availableCars);
    }

    public function test_returns_active_cars(): void 
    {
        // Given
        $station = $this->getStationForName('München');
        
        // When
        $availableCars = $this->carRepository->getAllActiveCarsOfStation($station->getId());

        // Then
        $this->assertNotEmpty($availableCars);
        foreach ($availableCars as $car) {
            $this->assertTrue($car->isActive());
        }
    }

    private function getStationForName(string $stationName): Station
    {
        return $this->stationRepository->findOneBy(['name' => $stationName]);
    }
}
