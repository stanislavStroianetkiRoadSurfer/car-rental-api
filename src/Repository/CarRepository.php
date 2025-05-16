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

    /**
     * @return array|Car[]
     */
    public function getAllActiveCarsOfStation(int $stationId): array
    {
        // Once more: this only works under the assumption that a car is assigned to the same station
        // always, else we'd need some datasource being updated to the predicted station at pickup time...

        return $this->createQueryBuilder('c')
            ->andWhere('c.station = :stationId')
            ->setParameter('stationId', $stationId)
            ->andWhere('c.active = TRUE')
            ->getQuery()
            ->getResult();
    }
}
