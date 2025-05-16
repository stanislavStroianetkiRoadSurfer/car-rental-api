<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Car;

interface CarRepositoryInterface
{
    /**
     * @return array|Car[]
     */
    public function getAllActiveCarsOfStation(int $stationId): array;
}
