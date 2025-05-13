<?php

declare(strict_types=1);

namespace App\Tests\Unit\ViewModel;

use App\Entity\Station;
use App\ViewModel\StationViewModel;
use PHPUnit\Framework\TestCase;

class StationViewModelTest extends TestCase
{
    public function test_serialize(): void
    {
        $id = 1;
        $name = 'Berlin';
        $city = 'Berlin';

        $station = (new Station())->setId($id)->setName($name)->setCity($city);

        $stationViewModel = StationViewModel::fromStation($station);
        /** @phpstan-ignore-next-line */
        $result = json_decode(json_encode($stationViewModel), true);

        self::assertIsArray($result);
        self::assertSame(
            [
                'id' => $id,
                'name' => $name,
                'city' => $city,
            ],
            $result
        );
    }
}
