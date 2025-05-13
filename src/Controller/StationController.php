<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\StationRepository;
use App\ViewModel\StationsViewModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StationController extends AbstractController
{
    #[Route('/stations', name: 'station_list', methods: ['GET'])]
    public function list(StationRepository $stationRepository): JsonResponse
    {
        $stations = $stationRepository->findAll();
        $stationViewModel = StationsViewModel::fromStations($stations);

        return new JsonResponse($stationViewModel, Response::HTTP_OK);
    }
}
