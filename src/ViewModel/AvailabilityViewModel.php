<?php

declare(strict_types=1);

namespace App\ViewModel;

final readonly class AvailabilityViewModel implements \JsonSerializable
{
    public function __construct(
        private readonly int $carId,
        private readonly string $carModelName,
        private readonly int $stationId,
        private readonly float $price, // potentially introduce value object here with currency
        private readonly bool $onlyForPremium,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'car_id' => $this->carId,
            'car_model' => $this->carModelName,
            'station_id' => $this->stationId,
            'price' => $this->price,
            'only_for_premium' => $this->onlyForPremium,
        ];
    }
}
