<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Car;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Car>
 */
class CarRepository extends ServiceEntityRepository implements CarRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
    }

    public function getAvailableCars(int $stationId, \DateTimeInterface $from, \DateTimeInterface $to)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.station = :stationId')
            ->setParameter('stationId', $stationId)
            ->getQuery()
            ->getResult();
    }
}
