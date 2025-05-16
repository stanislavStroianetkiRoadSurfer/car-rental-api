<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\Entity\Car;
use App\Entity\Station;
use App\Repository\BookingRepository;
use App\Repository\CarRepository;
use App\Repository\StationRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BookingRepositoryTest extends KernelTestCase
{
    private BookingRepository $bookingRepository;

    private StationRepository $stationRepository;

    private CarRepository $carRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->bookingRepository = $container->get(BookingRepository::class);
        $this->stationRepository = $container->get(StationRepository::class);
        $this->carRepository = $container->get(CarRepository::class);
    }

    public function test_returns_no_bookings_for_empty_car_ids(): void
    {
        // Given
        $carIds = [];
        $fromDate = $this->createDateTime('2025-04-12 09:00'); 
        $toDate = $this->createDateTime('2025-04-20 15:00');
        
        // When
        $bookings = $this->bookingRepository->getBookingsForCarsDuringTimeframe($carIds, $fromDate, $toDate);

        // Then
        $this->assertEmpty($bookings);
    }

    public function test_returns_the_particular_first_booking_from_fixtures(): void
    {
        // Given
        $carIds = array_keys($this->getActiveCarsForStationOfName('München'));
        $fromDate = $this->createDateTime('2025-04-12 11:00'); 
        $toDate = $this->createDateTime('2025-04-20 16:00');

        // When
        $bookings = $this->bookingRepository->getBookingsForCarsDuringTimeframe($carIds, $fromDate, $toDate);

        // Then
        $this->assertNotEmpty($bookings);
    }

    private function getStationForName(string $stationName): Station
    {
        return $this->stationRepository->findOneBy(['name' => $stationName]);
    }

    /**
     * Returns an array of Cars, indexed by Car id for faster access
     * @return array|Car[]
     */
    private function getActiveCarsForStationOfName(string $stationName): array
    {
        $station = $this->getStationForName($stationName);

        $activeCars = $this->carRepository->getAllActiveCarsOfStation($station->getId());

        $indexedCars = [];
        foreach ($activeCars as $car) {
            $indexedCars[$car->getId()] = $car;
        }

        return $indexedCars;
    }


    private function createDateTime(string $datetime): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat('Y-m-d H:i', $datetime);
    }
}
