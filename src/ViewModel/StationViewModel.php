<?php

declare(strict_types=1);

namespace App\ViewModel;

use App\Entity\Station;

final readonly class StationViewModel implements \JsonSerializable
{
    private function __construct(
        private Station $station,
    ) {
    }

    public static function fromStation(Station $station): self
    {
        return new self($station);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->station->getId(),
            'name' => $this->station->getName(),
            'city' => $this->station->getCity(),
        ];
    }
}
