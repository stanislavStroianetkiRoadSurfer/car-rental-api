<?php

declare(strict_types=1);

namespace App\Repository;

use App\Const\BookingStatusConst;
use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository implements BookingRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function getBookingsForCarsDuringTimeframe(array $carIds, \DateTimeInterface $from, \DateTimeInterface $to): array
    {
        // Todo: logic copied over from example PR, needs to be tested thoroughly
        // sadly automated tests have an issue with loading booking fixtures...
        // introduced the testing command instead that is being applied on the dev db, not test db
        // testing of common use cases and some "edge cases" seem to confirm that the query logic works
        //
        // depending on the query to be used in the end, it should be considered to analyse the
        // query carefully using EXPLAIN and to create an index to improve the read performance
        return $this->createQueryBuilder('b')
            ->andWhere('b.car IN (:carIds)')
            ->andWhere('b.startDate < :to')
            ->andWhere('b.endDate > :from')
            ->andWhere('b.status NOT IN (:statuses)')
            ->setParameters(
                [
                    'carIds' => $carIds,
                    'from' => $from,
                    'to' => $to,
                    'statuses' => BookingStatusConst::UNBLOCKING_STATUSES,
                ]
            )
            ->getQuery()
            ->getResult();
    }
}
