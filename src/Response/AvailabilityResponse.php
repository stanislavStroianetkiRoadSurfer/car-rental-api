<?php

declare(strict_types=1);

namespace App\Response;

final readonly class AvailabilityResponse implements \JsonSerializable
{
    public function __construct(
        public int $carId,
        public string $carModelName,
        public string $stationName,
        public float $price,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->carId,
            'model' => $this->carModelName,
            'station' => $this->stationName,
            'price' => $this->price,
            'onlyForPremium' => $this->calculateOnlyForPremium(),
        ];
    }

    private function calculateOnlyForPremium(): bool
    {
        return ('California Beach' === $this->carModelName && $this->price > 100)
            || ('California Ocean' === $this->carModelName && $this->price > 120)
            || ('Surfer Suite' === $this->carModelName && $this->price > 150);
    }
}
