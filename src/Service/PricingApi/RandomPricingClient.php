<?php

declare(strict_types=1);

namespace App\Service\PricingApi;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When('dev')]
#[When('test')]
class RandomPricingClient implements PricingClientInterface
{
    public function __construct(
        #[Autowire(env: 'PRICING_API_URL')]
        private readonly string $pricingServiceUrl,
    ) {
    }

    public function calculatePrices(
        int $stationId,
        array $carIds,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        string $currency,
    ): array {
        $rentalPrices = [];
        foreach ($carIds as $carId) {
            if (random_int(0,5) === 0) {
                continue;
            }

            $rentalPrices[] = new RentalPrice(
                $stationId, 
                $carId,
                $currency,
                (float) random_int(50,450),
            );
        }

        return $rentalPrices;
    }
}