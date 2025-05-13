<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Station;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StationFixtures extends Fixture
{
    private array $stations = [
        [
            'name' => 'München',
            'city' => 'München',
        ],
        [
            'name' => 'Frankfurt',
            'city' => 'Frankfurt',
        ],
        [
            'name' => 'Berlin',
            'city' => 'Berlin',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach ($this->stations as $station) {
            $stationEntity = new Station();
            $stationEntity->setName($station['name']);
            $stationEntity->setCity($station['city']);

            $manager->persist($stationEntity);

            $this->addReference('station_' . mb_strtolower((string) $station['city']), $stationEntity);
        }

        $manager->flush();
    }
}
