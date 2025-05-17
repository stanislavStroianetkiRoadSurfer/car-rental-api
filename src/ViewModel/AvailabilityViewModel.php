<?php

declare(strict_types=1);

namespace App\ViewModel;

final readonly class AvailabilityViewModel implements \JsonSerializable
{
    public function __construct(
        public readonly int $carId,
        public readonly string $carModelName,
        public readonly int $stationId,
        public readonly PriceViewModel $price,
        public readonly bool $onlyForPremium,
    ) {}

    public function jsonSerialize(): mixed
    {
        return [
            'car_id' => $this->carId,
            'car_model' => $this->carModelName,
            'station_id' => $this->stationId,
            'price' => $this->price->jsonSerialize(),
            'only_for_premium' => $this->onlyForPremium,
        ];
    }
}
