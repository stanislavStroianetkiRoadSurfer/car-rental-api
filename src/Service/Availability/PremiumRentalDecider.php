<?php

declare(strict_types=1);

namespace App\Service\Availability;

use App\Entity\Car;
use App\Service\PricingApi\RentalPrice;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PremiumRentalDecider 
{
    public function __construct(
        #[Autowire(param: 'premium_model_thresholds')]
        private readonly array $modelThresholds
    ) {}
    
    public function isPremiumOnly(Car $car, RentalPrice $rental): bool
    {
        // with the idea of moving things into the db, the threshold could be retrieved like this instead
        // $car->getModel()->getPremiumThreshold()
        // having the need for multiple currencies, it'll get more complicated of course...
        return isset($this->modelThresholds[$car->getModel()]) && $rental->price > $this->modelThresholds[$car->getModel()];
    }
}