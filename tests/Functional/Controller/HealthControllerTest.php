<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HealthControllerTest extends WebTestCase
{
    public function test_successful_health_response(): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/health');

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}
