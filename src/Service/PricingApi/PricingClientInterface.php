<?php

declare(strict_types=1);

namespace App\Service\PricingApi;

interface PricingClientInterface
{
    /**
     * @param array|int[] $carIds
     * @return array|RentalPrice[]
     */
    public function calculatePrices(
        int $stationId,
        array $carIds, // as mentioned, maybe should be changed to price per model, not individual car?
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        string $currency,
    ): array;
}