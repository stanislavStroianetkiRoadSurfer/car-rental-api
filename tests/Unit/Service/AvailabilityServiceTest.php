<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Request\AvailabilityRequest;
use App\Service\Availability\AvailableCarsFetcher;
use App\Service\Availability\PremiumRentalDecider;
use App\Service\AvailabilityService;
use App\Service\PricingApi\PricingClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AvailabilityServiceTest extends KernelTestCase
{
    private AvailabilityService $availabilityService;

    private AvailableCarsFetcher&MockObject $carsFetcher;

    private PricingClientInterface&MockObject $pricingClient;

    private PremiumRentalDecider&MockObject $premiumDecider;

    protected function setUp(): void
    {
        $this->carsFetcher = $this->createMock(AvailableCarsFetcher::class);
        $this->pricingClient = $this->createMock(PricingClientInterface::class);
        $this->premiumDecider = $this->createMock(PremiumRentalDecider::class);

        $this->availabilityService = new AvailabilityService(
             $this->carsFetcher, 
             $this->pricingClient,
             $this->premiumDecider,
        );
    }

    public function test_returns_empty_array_for_station_without_available_cars(): void
    {
        $stationId = 534;
        $startDate = '2026-05-20 10:30';
        $endDate = '2026-05-30 15:30';
        
        // Given
        $this->carsFetcher->expects($this->once())
            ->method('getActiveCarsWithoutBookingsDuringTimeframe')
            ->with($stationId, $this->createDateTime($startDate), $this->createDateTime($endDate))
            ->willReturn([]);
        // would prefer using a Spy here down in the Then stage, but PHPUnit's default Mocks don't support
        // spying via Invocations anymore...
        $this->pricingClient->expects($this->never()) 
            ->method('calculatePrices');


        // When
        $request = $this->createRequest(
            $stationId,
            $startDate,
             $endDate,
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

    private function createDateTime(string $datetime): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat('Y-m-d H:i', $datetime);
    }

}
