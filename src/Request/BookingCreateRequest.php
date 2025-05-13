<?php

declare(strict_types=1);

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class BookingCreateRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('int')]
        public readonly int $carId,
        #[Assert\NotBlank]
        #[Assert\Email]
        public readonly string $email,
    ) {
    }

    public function getCarId(): int
    {
        return $this->carId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
