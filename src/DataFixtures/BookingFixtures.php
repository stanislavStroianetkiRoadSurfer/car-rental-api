<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Const\BookingStatusConst;
use App\Entity\Booking;
use App\Entity\Car;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BookingFixtures extends Fixture implements DependentFixtureInterface
{
    private array $bookings = [
        [
            'car_ref' => 'car_muc_1',
            'status' => BookingStatusConst::CONFIRMED,
            'startDate' => '2025-04-12 09:00',
            'endDate' => '2025-04-20 15:00',
            'customerEmail' => 'foo@roadsurfer.de',
        ],
        [
            'car_ref' => 'car_muc_1',
            'status' => BookingStatusConst::PENDING,
            'startDate' => '2025-05-12 10:30',
            'endDate' => '2025-05-22 20:00',
            'customerEmail' => 'foo@roadsurfer.de',
        ],
        [
            'car_ref' => 'car_muc_1',
            'status' => BookingStatusConst::ON_GOING,
            'startDate' => '2025-05-21 12:00',
            'endDate' => '2025-05-30 11:00',
            'customerEmail' => 'foo@roadsurfer.de',
        ],
        [
            'car_ref' => 'car_muc_2',
            'status' => BookingStatusConst::CANCELLED,
            'startDate' => '2025-04-12 9:00',
            'endDate' => '2025-06-20 10:00',
            'customerEmail' => 'foo@roadsurfer.de',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach ($this->bookings as $bookingData) {
            $booking = new Booking();
            $booking->setCar($this->getReference($bookingData['car_ref'], Car::class));
            $booking->setStatus($bookingData['status']);
            $booking->setStartDate(\DateTimeImmutable::createFromFormat('Y-m-d H:i', $bookingData['startDate']));
            $booking->setEndDate(\DateTimeImmutable::createFromFormat('Y-m-d H:i', $bookingData['endDate']));
            // While the Booking class claims customer email can be null, the db schema does not allow it...
            $booking->setCustomerEmail($bookingData['customerEmail']);

            $manager->persist($booking);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CarFixtures::class,
        ];
    }
}
