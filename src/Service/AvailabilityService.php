<?php

declare(strict_types=1);

namespace App\Service;

use App\Request\AvailabilityRequest;
use App\Service\Availability\AvailableCarsFetcher;
use App\Service\Availability\PremiumRentalDecider;
use App\Service\PricingApi\PricingClientInterface;
use App\ViewModel\AvailabilityViewModel;
use App\ViewModel\PriceViewModel;

class AvailabilityService
{
    public function __construct(
        private readonly AvailableCarsFetcher $availableCarsFetcher,
        private readonly PricingClientInterface $pricingClient,
        private readonly PremiumRentalDecider $premiumDecider,
    ) {}

    public function getAvailableCarsWithPrices(
        AvailabilityRequest $request,
    ): array {
        $availableCars = $this->availableCarsFetcher->getActiveCarsWithoutBookingsDuringTimeframe(
            $request->getStationId(), 
            $request->getStartDate(), 
            $request->getEndDate(),
        );

        if (count($availableCars) === 0) {
            return [];
        }

        $rentalPrices = $this->pricingClient->calculatePrices(
            $request->getStationId(),
            array_keys($availableCars),
            $request->getStartDate(),
            $request->getEndDate(),
            'EUR'
        );

        $carsWithPricing = [];
        foreach ($rentalPrices as $rentalPrice) {
            $car = $availableCars[$rentalPrice->carId];
            $onlyForPremium = $this->premiumDecider->isPremiumOnly($car, $rentalPrice);
            $price = new PriceViewModel($rentalPrice->price, $rentalPrice->currency);

            $carsWithPricing[] = new AvailabilityViewModel(
                $car->getId(),
                $car->getModel(),
                $request->getStationId(),
                $price,
                $onlyForPremium,
            );
        }

        return $carsWithPricing;
    }
}