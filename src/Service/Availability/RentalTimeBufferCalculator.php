<?php

declare(strict_types=1);

namespace App\Service\Availability;

class RentalTimeBufferCalculator 
{    
    public function getAdjustedStartDatetime(\DateTimeImmutable $rentalStartDate): \DateTimeInterface
    {
        // We could add logic to limit things to working hours, bank holidays, etc as well, 
        // potentially by injecting other services via constructor/DIC
        // For simplification, we "just" add a buffer of 24 hours

        return $rentalStartDate->modify('-24 hours');
    }

    public function getAdjustedEndDatetime(\DateTimeImmutable $rentalEndDate): \DateTimeInterface
    {
        return $rentalEndDate->modify('+24 hours');
    }

}