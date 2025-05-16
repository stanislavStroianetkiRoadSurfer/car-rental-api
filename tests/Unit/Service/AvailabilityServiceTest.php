<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Repository\BookingRepositoryInterface;
use App\Repository\CarRepositoryInterface;
use App\Request\AvailabilityRequest;
use App\Service\AvailabilityService;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AvailabilityServiceTest extends KernelTestCase
{
    private AvailabilityService $availabilityService;

    private CarRepositoryInterface&MockObject $carRepository;

    private BookingRepositoryInterface&MockObject $bookingRepository;

    protected function setUp(): void
    {
        $this->carRepository = $this->createMock(CarRepositoryInterface::class);
        $this->bookingRepository = $this->createMock(BookingRepositoryInterface::class);

        $this->availabilityService = new AvailabilityService(
            $this->carRepository, 
            $this->bookingRepository
        );
    }

    public function test_no_available_cars_for_station_without_active_cars(): void
    {
        $stationId = 534;
        
        // Given
        $this->carRepository->expects($this->once())
            ->method('getAllActiveCarsOfStation')
            ->with($stationId)
            ->willReturn([]);
        // would prefer using a Spy here down in the Then stage, but PHPUnit's default Mocks don't support
        // spying via Invocations anymore...
        $this->bookingRepository->expects($this->never()) 
            ->method('getBookingsForCarsDuringTimeframe');


        // When
        $request = $this->createRequest(
            $stationId,
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
}
