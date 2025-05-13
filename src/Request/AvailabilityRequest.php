<?php

declare(strict_types=1);

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class AvailabilityRequest
{
    public function __construct(
        #[Assert\GreaterThan(0)]
        public int $stationId,
        #[Assert\DateTime(format: 'Y-m-d')]
        public string $startDate,
        #[Assert\DateTime(format: 'Y-m-d')]
        public string $endDate,
    ) {
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->startDate);
    }

    public function getEndDate(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->endDate);
    }
}
