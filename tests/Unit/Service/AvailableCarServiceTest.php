<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Car;
use App\Entity\Station;
use App\Repository\BookingRepository;
use App\Repository\CarRepository;
use App\Request\AvailabilityRequest;
use App\Response\AvailabilityResponse;
use App\Service\AvailableCarService;
use App\Service\PricingApi\PricingClientInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class AvailableCarServiceTest extends KernelTestCase
{
    public function test_successful(): void
    {
        $station = new Station();
        $station->setId(1);
        $station->setName('Test Station');

        $car = new Car();
        $car->setId(1);
        $car->setModel('Test Model');
        $car->setActive(true);
        $car->setStation($station);

        $carRepo = $this->createMock(CarRepository::class);
        $carRepo->expects($this->once())
            ->method('getByStationId')
            ->with(1)
            ->willReturn([$car]);

        $bookingRepo = $this->createMock(BookingRepository::class);
        $bookingRepo->expects($this->once())
            ->method('getBookingsForCarInPeriod')
            ->with($car, new \DateTime('2025-04-01'), new \DateTime('2025-04-07'))
            ->willReturn([]);

        $pricingClientInterfaceMock = $this->createMock(PricingClientInterface::class);

        $service = new AvailableCarService($carRepo, $bookingRepo, $pricingClientInterfaceMock, new ArrayAdapter());

        $result = $service->getAvailableCars(new AvailabilityRequest(1, '2025-04-01', '2025-04-07'));

        $this->assertCount(1, $result);
        $this->assertEquals(
            new AvailabilityResponse(
                1,
                'Test Model',
                'Test Station',
                0.0
            ),
            $result[0]
        );
    }
}
