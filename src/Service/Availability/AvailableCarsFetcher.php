<?php

declare(strict_types=1);

namespace App\Service\Availability;

use App\Const\BookingStatusConst;
use App\Entity\Car;
use App\Repository\BookingRepositoryInterface;
use App\Repository\CarRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AvailableCarsFetcher
{
    public function __construct(
        private readonly CarRepositoryInterface $carRepository,
        private readonly BookingRepositoryInterface $bookingRepository,
        private readonly RentalTimeBufferCalculator $rentalTimeBufferCalculator,
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * Providing two options here, one simple, leaning on the example PR, one where that logic is "converted" into a single query
     * Placing the logic of both into one file isn't the way to go if both would reside in the codebase.
     * This is rather for demonstration purposes of my pathway though.
     * 
     * Mid term, depending on other business logic expectations, the code might end up being a mix of both an initial single query 
     * plus additionally some more logic inside the php application.
     * 
     * As mentioned elsewhere, the read performance of both approaches can be improved by introducing appropriate indexes to the db.
     * 
     * @return array|Car[] Array indexed by the car's id for easier retrieval. Improvement idea: a dedicated collection class
     */
    public function getActiveCarsWithoutBookingsDuringTimeframe(
        int $stationId,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
    ): array {
        // 
        
        
        //return $this->simpleSolutionCloserToExampePR($stationId, $startDate, $endDate);
        
        return $this->rawSQLQueryMappedToEntitiesSolution($stationId, $startDate, $endDate);
    }

    /**
     * This approach is more or less an improvement of the solution of the example PR running two queries.
     */
    private function simpleSolutionCloserToExampePR(int $stationId, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $activeCarsIndexedByCarId = $this->getActiveCarsByStationIndexedByCarId($stationId);

        if (count($activeCarsIndexedByCarId) == 0) {
            return [];
        } 

        $relevantBookingForCars = $this->getRelevantBookingsForCarsDuringTimeframe(array_keys($activeCarsIndexedByCarId), $startDate, $endDate);

        foreach ($relevantBookingForCars as $booking) {
            unset($activeCarsIndexedByCarId[$booking->getCar()->getId()]);
        }

        return array_values($activeCarsIndexedByCarId);
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

    /**
     * Making use of a single query with a subquery, needing some manual steps to get results hydrated into Doctrine entities.
     */
    private function rawSQLQueryMappedToEntitiesSolution(int $stationId, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $bookingStatusStatementInClause = '';
        // some "ugly" looking code here as forraw sql queries one needs to take care of resolving IN clauses manually...
        $statementBinds = ['stationId' => $stationId, 
            'startDate' => $this->rentalTimeBufferCalculator->getAdjustedStartDatetime($startDate)->format('Y-m-d H:i'), 
            'endDate' => $this->rentalTimeBufferCalculator->getAdjustedEndDatetime($endDate)->format('Y-m-d H:i'),
        ];
        foreach (BookingStatusConst::UNBLOCKING_STATUSES as $key => $status) {
            if (!empty($bookingStatusStatementInClause)) {
                $bookingStatusStatementInClause .= ', ';
            }
            $bookingStatusStatementInClause .= ':bookingStatus'.$key;
            $statementBinds['bookingStatus'.$key] = $status;
        }

        $sql = 'SELECT c.* FROM car c WHERE c.station_id = :stationId AND c.active = TRUE AND c.id NOT IN ('
            . 'SELECT b.car_id FROM booking b WHERE b.start_date < :endDate AND b.end_date > :startDate'
            . (!empty($bookingStatusStatementInClause)?' AND b.status NOT IN(' . $bookingStatusStatementInClause .')':'')
            . ')';

        $rsm = new ResultSetMappingBuilder($this->em);
        $rsm->addRootEntityFromClassMetadata('App\Entity\Car', 'c');

        $nativeQuery = $this->em->createNativeQuery($sql, $rsm);
        $nativeQuery->setParameters($statementBinds);

        $fetchedCars = $nativeQuery->getResult();
        $indexedCars = [];
        foreach ($fetchedCars as $car) {
            $indexedCars[$car->getId()] = $car;
        }
        
        return $indexedCars;
    }
}