<?php

declare(strict_types=1);

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class AvailabilityRequest
{
    public function __construct(
        #[Assert\GreaterThan(0)]
        private readonly int $stationId,
        #[Assert\DateTime(format: 'Y-m-d H:i')]
        private readonly string $startDate,
        #[Assert\DateTime(format: 'Y-m-d H:i')]
        private readonly string $endDate,
    ) {
    }

    public function getStationId(): int
    {
        return $this->stationId;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat('Y-m-d H:i', $this->startDate);
    }

    public function getEndDate(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat('Y-m-d H:i', $this->endDate);
    }
}
