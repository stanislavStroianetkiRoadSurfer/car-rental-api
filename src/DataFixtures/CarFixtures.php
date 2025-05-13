<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Car;
use App\Entity\Station;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CarFixtures extends Fixture implements DependentFixtureInterface
{
    private array $cars = [
        [
            'model' => 'Volkswagen California',
            'station_ref' => 'station_münchen',
            'active' => true,
        ],
        [
            'model' => 'Mercedes Marco Polo',
            'station_ref' => 'station_frankfurt',
            'active' => true,
        ],
        [
            'model' => 'Ford Nugget',
            'station_ref' => 'station_berlin',
            'active' => false,
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach ($this->cars as $carData) {
            $car = new Car();
            $car->setModel($carData['model']);
            $car->setActive($carData['active']);
            $car->setStation($this->getReference($carData['station_ref'], Station::class));

            $manager->persist($car);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            StationFixtures::class,
        ];
    }
}
