<?php

declare(strict_types=1);

namespace App\Tests;

use App\Tests\Subscriber\InitDatabaseSubscriber;
use PHPUnit\Runner;
use PHPUnit\TextUI;

final class InitDatabaseExtension implements Runner\Extension\Extension
{
    public function bootstrap(
        TextUI\Configuration\Configuration $configuration,
        Runner\Extension\Facade $facade,
        Runner\Extension\ParameterCollection $parameters,
    ): void {
        $facade->registerSubscribers(
            new InitDatabaseSubscriber()
        );
    }
}
