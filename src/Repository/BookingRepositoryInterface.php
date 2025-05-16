<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Booking;

interface BookingRepositoryInterface
{
    /**
     * @return array|Booking[]
     */
    public function getBookingsForCarsDuringTimeframe(array $carIds, \DateTimeInterface $from, \DateTimeInterface $to): array;
}
