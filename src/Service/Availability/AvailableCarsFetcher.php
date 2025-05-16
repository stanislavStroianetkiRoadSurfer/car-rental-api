<?php

declare(strict_types=1);

namespace App\Service\Availability;

use App\Repository\BookingRepositoryInterface;
use App\Repository\CarRepositoryInterface;

class AvailableCarsFetcher
{
    public function __construct(
        private readonly CarRepositoryInterface $carRepository,
        private readonly BookingRepositoryInterface $bookingRepository,
        private readonly RentalTimeBufferCalculator $rentalTimeBufferCalculator,
    ) {}

    public function getActiveCarsWithoutBookingsDuringTimeframe(
        int $stationId,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
    ): array {
        // Todo: Writing the most efficient single query to get all cars that do *not* have a booking 
        // during the timeframe is a bit tricky after some time invested, it feels like it would need a 
        // subquery ideally, which isn't thaaaat straight forward with Doctrine. 
        // I will follow a slightly more "naive" approach for now similiar to the example PR, but with 
        // less db queries and would potentially look deeper into it if the time allows it.
        
        $activeCarsIndexedByCarId = $this->getActiveCarsByStationIndexedByCarId($stationId);

        if (count($activeCarsIndexedByCarId) == 0) {
            return [];
        } 

        $relevantBookingForCars = $this->getRelevantBookingsForCarsDuringTimeframe(array_keys($activeCarsIndexedByCarId), $startDate, $endDate);

        foreach ($relevantBookingForCars as $booking) {
            unset($activeCarsIndexedByCarId[$booking->getCar()->getId()]);
        }

        return $activeCarsIndexedByCarId;
    }

    private function getActiveCarsByStationIndexedByCarId(int $stationId): array
    {
        $activeCars = $this->carRepository->getAllActiveCarsOfStation($stationId);

        $indexed = [];
        foreach($activeCars as $car) {
            $indexed[$car->getId()] = $car;
        }

        return $indexed;
    }

    private function getRelevantBookingsForCarsDuringTimeframe(
        array $carIds, 
        \DateTimeInterface $startDate, 
        \DateTimeInterface $endDate
    ): array 
    {
        return $this->bookingRepository->getBookingsForCarsDuringTimeframe(
            $carIds, 
            $this->rentalTimeBufferCalculator->getAdjustedStartDatetime($startDate), 
            $this->rentalTimeBufferCalculator->getAdjustedEndDatetime($endDate),
        );
    }
}