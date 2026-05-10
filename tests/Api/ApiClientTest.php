<?php

namespace Tests\Api;

use Anthony\EplDashboard\Api\ApiClient;
use PHPUnit\Framework\TestCase;

class ApiClientTest extends TestCase
{
    private ApiClient $client;

    protected function setUp(): void
    {
        $_ENV['API_BASE_URL'] = 'https://api.football-data.org/v4';
        $_ENV['API_KEY']      = 'test_invalid_key';
    }

    public function test_get_throws_exception_on_invalid_api_key(): void
    {
        $client = new ApiClient();

        $this->expectException(\RuntimeException::class);

        $client->get('/competitions/PL/standings');
    }

    public function test_get_throws_exception_on_invalid_endpoint(): void
    {
        $client = new ApiClient();

        $this->expectException(\RuntimeException::class);

        $client->get('/this/endpoint/does/not/exist');
    }
}
