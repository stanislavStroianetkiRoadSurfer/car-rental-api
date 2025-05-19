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
            'car_ref' => 'car_muc_1',
        ],
        [
            'model' => 'Mercedes Marco Polo',
            'station_ref' => 'station_münchen',
            'active' => true,
            'car_ref' => 'car_muc_2',
        ],
        [
            'model' => 'California Beach',
            'station_ref' => 'station_münchen',
            'active' => true,
            'car_ref' => 'car_muc_3',
        ],
        [
            'model' => 'Ford Nugget',
            'station_ref' => 'station_münchen',
            'active' => false,
            'car_ref' => 'car_muc_4',
        ],
        [
            'model' => 'Mercedes Marco Polo',
            'station_ref' => 'station_frankfurt',
            'active' => true,
            'car_ref' => 'car_fra_1',
        ],
        [
            'model' => 'Ford Nugget',
            'station_ref' => 'station_berlin',
            'active' => false,
            'car_ref' => 'car_ber_1',
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

            // using a hardcoded ref as no other property combination is considered "unique" mid term
            // (there might be more than one car model per station quickly, also in fictures)
            $this->addReference($carData['car_ref'], $car); 
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
