<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\StationRepository;
use App\Service\Availability\AvailableCarsFetcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This file is the attempt to have a workaround to test the fetching logic with a connection to aproper mysql database.
 * As mentioned, for whatever reasons the automated testing environment on my machine is not loading the booking fixtures, 
 * making these tests impossible.
 */
#[AsCommand(name: 'test:availabilities')]
class TestAvailabilityCommand extends Command
{  
    public function __construct(
        private readonly AvailableCarsFetcher $carsFetcher,
        private readonly StationRepository $stationRepository,
    ) {
        parent::__construct();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    { 
        $station = $this->stationRepository->findOneBy(['name' => 'München']);

        $noErrors = true;
        foreach ($this->dataProvider() as $testRun) {
            $output->writeln($testRun['testCaseDescription']);
            $output->writeln('From: ' . $testRun['startDate'] . ' To: ' . $testRun['endDate']);
            $output->writeln('Expected num of available cars: ' . $testRun['expectedNumOfAvailableCars']);

            $availableCars = $this->carsFetcher->getActiveCarsWithoutBookingsDuringTimeframe(
                $station->getId(),
                $this->createDatetime($testRun['startDate']),
                $this->createDatetime($testRun['endDate']),
            );

            if (count($availableCars) !== $testRun['expectedNumOfAvailableCars']) {
                $output->writeln('Returned num of available cars DOES NOT MATCH the expectation: ' . count($availableCars));
                $output->writeln('[DEBUG] Car ids: ' . implode(', ', array_keys($availableCars)));
                $noErrors = false;
            } else {
                $output->writeln('Returned num of available cars MATCHES the expectation.');
            }
            $output->writeln('');
        }

        if ($noErrors) {
            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }

    private function dataProvider(): array
    {
        return [
            [
                'testCaseDescription' => 'timeframe without fixture bookings should return all cars of the station',
                'startDate' => '2026-05-05 9:00',
                'endDate' => '2026-05-05 15:00',
                'expectedNumOfAvailableCars' => 3,
            ],
            [
                'testCaseDescription' => 'timeframe is *within* the valid booking for car 2 of fixtures',
                'startDate' => '2025-06-14 9:00',
                'endDate' => '2025-06-16 15:00',
                'expectedNumOfAvailableCars' => 2,
            ],
            [
                'testCaseDescription' => 'timeframe is *within* the *cancelled* booking for car 2 of fixtures',
                'startDate' => '2025-07-14 10:00',
                'endDate' => '2025-07-16 15:00',
                'expectedNumOfAvailableCars' => 3,
            ],
            [
                'testCaseDescription' => 'timeframe starts before, but leads into the valid booking for car 2 of fixtures',
                'startDate' => '2025-06-08 9:00',
                'endDate' => '2025-06-15 15:00',
                'expectedNumOfAvailableCars' => 2,
            ],
            [
                'testCaseDescription' => 'timeframe starts within, but ends after of the valid booking for car 2 of fixtures',
                'startDate' => '2025-06-15 9:00',
                'endDate' => '2025-06-25 15:00',
                'expectedNumOfAvailableCars' => 2,
            ],
            [
                'testCaseDescription' => 'timeframe starts before and ends after the valid booking for car 2 of fixtures',
                'startDate' => '2025-06-05 9:00',
                'endDate' => '2025-06-25 15:00',
                'expectedNumOfAvailableCars' => 2,
            ],
            [
                'testCaseDescription' => 'timeframe starts within 24h buffer of the end of valid booking for car 2 of fixtures',
                'startDate' => '2025-06-21 09:00',
                'endDate' => '2025-06-25 15:00',
                'expectedNumOfAvailableCars' => 2,
            ],
            [
                'testCaseDescription' => 'timeframe ends within 24h buffer of the start of valid booking for car 2 of fixtures',
                'startDate' => '2025-06-01 10:00',
                'endDate' => '2025-06-11 15:00',
                'expectedNumOfAvailableCars' => 2,
            ],
            [
                'testCaseDescription' => 'timeframe to overlap with bookings for cars 1 *and* 2',
                'startDate' => '2025-04-01 10:00',
                'endDate' => '2025-06-21 15:00',
                'expectedNumOfAvailableCars' => 1,
            ],
        ];
    }

    private function createDatetime(string $dateString) 
    {
        return \DateTimeImmutable::createFromFormat('Y-m-d H:i', $dateString);
    }
}
