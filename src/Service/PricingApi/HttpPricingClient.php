<?php

declare(strict_types=1);

namespace App\Service\PricingApi;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When('prod')]
class HttpPricingClient implements PricingClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        #[Autowire(env: 'PRICING_API_URL')]
        private readonly string $pricingServiceUrl,
    ) {
    }

    public function calculatePrices(
        int $stationId,
        array $carIds,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        string $currency
    ): array {
        try {
            $response = $this->httpClient->request(
                'POST',
                $this->pricingServiceUrl,
                [
                    'json' => [
                        'stationId' => $stationId,
                        'carIds' => implode(',', $carIds),
                        'startDate' => $startDate->format('Y-m-d'),
                        'endDate' => $endDate->format('Y-m-d'),
                        'currency' => $currency,
                    ],
                ]
            );

            $data = $response->toArray();
        } catch (\Throwable) {
            return [];
        }

        $rentalPrices = [];
        foreach ($data as $priceInformation) {
            // no validation here as we entrust our price engine to either return proper data 
            // - or alternatively not return any data for the given car
            $rentalPrices[] = new RentalPrice(
                $stationId, 
                $priceInformation['carId'],
                $priceInformation['currency'],
                $priceInformation['price'],
            );
        }

        return $rentalPrices;
    }
}