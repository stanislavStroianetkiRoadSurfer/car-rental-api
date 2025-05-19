<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Availability;

use App\Entity\Car;
use App\Service\Availability\PremiumRentalDecider;
use App\Service\PricingApi\RentalPrice;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PremiumRentalDeciderTest extends KernelTestCase
{
    public function test_decides_for_not_premium_as_default(): void
    {
        // Given
        $decider = new PremiumRentalDecider([]);

        $car = new Car();
        $car->setModel('California Beach');

        $rentalPrice = new RentalPrice(
            123,
            234,
            'EUR',
            1000,
        );

        // When
        $resultDecision = $decider->isPremiumOnly($car, $rentalPrice);

        // Then
        $this->assertFalse($resultDecision);
    }

    public function test_decides_for_not_premium_if_below_configured_threshold(): void
    {
        // Given
        $modelName = 'BestModel';
        $decider = new PremiumRentalDecider([$modelName => 100]);

        $car = new Car();
        $car->setModel($modelName);

        $rentalPrice = new RentalPrice(
            123,
            234,
            'EUR',
            50,
        );

        // When
        $resultDecision = $decider->isPremiumOnly($car, $rentalPrice);

        // Then
        $this->assertFalse($resultDecision);
    }

    public function test_decides_for_premium_if_above_configured_threshold(): void
    {
        // Given
        $modelName = 'BestModel';
        $decider = new PremiumRentalDecider([$modelName => 100]);

        $car = new Car();
        $car->setModel($modelName);

        $rentalPrice = new RentalPrice(
            123,
            234,
            'EUR',
            150,
        );

        // When
        $resultDecision = $decider->isPremiumOnly($car, $rentalPrice);

        // Then
        $this->assertTrue($resultDecision);
    }
}
