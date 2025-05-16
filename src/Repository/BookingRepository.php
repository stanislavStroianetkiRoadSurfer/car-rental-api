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
