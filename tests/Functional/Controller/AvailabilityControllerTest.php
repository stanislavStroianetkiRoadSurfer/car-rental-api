<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AvailabilityControllerTest extends WebTestCase
{
    public function test_availabilities(): void
    {
        $client = static::createClient();

        $client->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, '/availabilities?station_id=1&from=2025-04-01&to=2025-04-07');

        $this->assertResponseIsSuccessful();

        $response = $client->getResponse();
        $data = json_decode((string) $response->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
    }
}
