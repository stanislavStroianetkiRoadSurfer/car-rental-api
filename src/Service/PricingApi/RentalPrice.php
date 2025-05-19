<?php

declare(strict_types=1);

namespace App\Service\PricingApi;

class RentalPrice
{
    // using float over int + delimiter position for prices may be considered bad practice here...
    public function __construct(
        public readonly int $stationId,
        public readonly int $carId,
        public readonly string $currency,
        public readonly float $price,
        // whatever properties more, the example PR contains "breakdown" data that isn't used anywhere...
    ) {}
}
