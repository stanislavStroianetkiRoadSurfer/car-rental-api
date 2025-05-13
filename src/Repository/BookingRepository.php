<?php

declare(strict_types=1);

namespace App\Repository;

use App\Const\BookingStatusConst;
use App\Entity\Booking;
use App\Entity\Car;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function getBookingsForCarInPeriod(Car $car, \DateTimeInterface $from, \DateTimeInterface $to): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.car = :car')
            ->andWhere('b.startDate < :to')
            ->andWhere('b.endDate > :from')
            ->andWhere('b.status IN (:statuses)')
            ->setParameters(
                [
                    'car' => $car,
                    'from' => $from,
                    'to' => $to,
                    'statuses' => BookingStatusConst::getBlockingStatuses(),
                ]
            )
            ->getQuery()
            ->getResult();
    }
}
