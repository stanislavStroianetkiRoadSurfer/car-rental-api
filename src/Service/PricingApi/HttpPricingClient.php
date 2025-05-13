<?php

declare(strict_types=1);

namespace App\Service\PricingApi;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpPricingClient implements PricingClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $pricingServiceUrl = 'http://pricing.local/api/price',
    ) {
    }

    public function calculatePrice(
        int $carId,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
    ): float {
        try {
            $response = $this->httpClient->request(
                'POST',
                $this->pricingServiceUrl,
                [
                    'json' => [
                        'car_id' => $carId,
                        'startDate' => $startDate->format('Y-m-d'),
                        'endDate' => $endDate->format('Y-m-d'),
                    ],
                ]
            );

            $data = $response->toArray();

            return $data['price'] ?? 0.0;
        } catch (\Throwable) {
            return 0.0;
        }
    }

    public function getPricingBreakdown(
        int $carId,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
    ): array {
        try {
            $response = $this->httpClient->request(
                'POST',
                $this->pricingServiceUrl,
                [
                    'json' => [
                        'car_id' => $carId,
                        'from' => $startDate->format('Y-m-d'),
                        'to' => $endDate->format('Y-m-d'),
                        'breakdown' => true,
                    ],
                ]
            );

            $data = $response->toArray();

            return [
                'base_price' => $data['base_price'] ?? 0.0,
                'discount' => $data['discount'] ?? 0.0,
                'surcharge' => $data['surcharge'] ?? 0.0,
                'tax' => $data['tax'] ?? 0.0,
                'final_price' => $data['final_price'] ?? 0.0,
            ];
        } catch (\Throwable) {
            return [
                'base_price' => 0.0,
                'discount' => 0.0,
                'surcharge' => 0.0,
                'tax' => 0.0,
                'final_price' => 0.0,
            ];
        }
    }
}
