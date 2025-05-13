<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service;

use App\Entity\Booking;
use App\Entity\Car;
use App\Request\BookingCreateRequest;
use App\Service\BookingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookingServiceTest extends KernelTestCase
{
    private BookingService $bookingService;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->bookingService = $container->get(BookingService::class);
        $this->entityManager = $container->get(EntityManagerInterface::class);
    }

    public function test_create_booking_successfully(): void
    {
        // Given
        $car = new Car();
        $car->setModel('Test Model');
        $car->setActive(true);
        $this->entityManager->persist($car);
        $this->entityManager->flush();

        $request = new BookingCreateRequest($car->getId(), 'test@example.com');

        // When
        $booking = $this->bookingService->create($request);

        // Then
        $this->assertInstanceOf(Booking::class, $booking);
        $this->assertSame('test@example.com', $booking->getCustomerEmail());
        $this->assertSame('pending', $booking->getStatus());
        $this->assertSame($car->getId(), $booking->getCar()->getId());

        $bookingInDb = $this->entityManager->getRepository(Booking::class)->find($booking->getId());
        $this->assertNotNull($bookingInDb);
    }

    public function test_create_booking_with_non_existent_car_throws_exception(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Car not found');

        $request = new BookingCreateRequest(999999, 'ghost@example.com');
        $this->bookingService->create($request);
    }
}
