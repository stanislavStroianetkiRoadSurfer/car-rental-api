<?php

declare(strict_types=1);

namespace App\Repository;

interface CarRepositoryInterface
{
    public function getAvailableCars(int $stationId, \DateTimeInterface $from, \DateTimeInterface $to);
}