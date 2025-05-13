<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Car;
use App\Request\BookingCreateRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookingService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function create(BookingCreateRequest $bookingCreateRequest): Booking
    {
        $car = $this->entityManager->getRepository(Car::class)->find($bookingCreateRequest->getCarId());

        if (!$car) {
            throw new NotFoundHttpException('Car not found');
        }

        $booking = new Booking();
        $booking->setCar($car);
        $booking->setCustomerEmail($bookingCreateRequest->getEmail());
        $booking->setStartDate(new \DateTimeImmutable());
        $booking->setEndDate(new \DateTimeImmutable('+1 day'));
        $booking->setStatus('pending');

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        return $booking;
    }
}
