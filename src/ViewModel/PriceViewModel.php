<?php

declare(strict_types=1);

namespace App\ViewModel;

final readonly class PriceViewModel implements \JsonSerializable
{
    public function __construct(
        public readonly float $amount, // as mentioned elsewhere, often price is stored/treated as int + decimal precision in e-commerce
        private readonly string $currency,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
    }
}
