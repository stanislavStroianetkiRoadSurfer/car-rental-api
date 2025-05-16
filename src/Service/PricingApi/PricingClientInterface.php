<?php

declare(strict_types=1);

namespace App\Service\PricingApi;

interface PricingClientInterface
{
    /**
     * @param int $stationId
     * @param array|int[] $carIds
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $endDate
     * @return array|RentalPrice[]
     */
    public function calculatePrices(
        int $stationId,
        array $carIds, // as mentioned, maybe should be changed to price per model, not individual car?
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
    ): array;
}