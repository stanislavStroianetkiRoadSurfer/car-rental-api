<?php

declare(strict_types=1);

namespace App\ViewModel;

use App\Entity\Station;

final readonly class StationsViewModel implements \JsonSerializable
{
    private function __construct(
        private array $stations,
    ) {
    }

    public static function fromStations(array $stations): self
    {
        return new self($stations);
    }

    public function jsonSerialize(): array
    {
        return array_map(
            static fn (Station $station): StationViewModel => StationViewModel::fromStation($station),
            $this->stations,
        );
    }
}
