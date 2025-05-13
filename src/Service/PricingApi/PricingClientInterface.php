<?php

declare(strict_types=1);

namespace App\Service\PricingApi;

interface PricingClientInterface
{
    public function calculatePrice(
        int $carId,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
    ): float;
}
