<?php

namespace Tests\Services;

use Anthony\EplDashboard\Api\ApiClient;
use Anthony\EplDashboard\Services\CacheService;
use Anthony\EplDashboard\Services\StandingsService;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PDO;
use PDOStatement;

class StandingsServiceTest extends TestCase
{
    private MockObject&ApiClient $apiMock;
    private MockObject&CacheService $cacheMock;
    private MockObject&PDO $pdoMock;
    private MockObject&PDOStatement $stmtMock;
    private StandingsService $standingsService;

    protected function setUp(): void
    {
        $this->apiMock   = $this->createMock(ApiClient::class);
        $this->cacheMock = $this->createMock(CacheService::class);
        $this->pdoMock   = $this->createMock(PDO::class);
        $this->stmtMock  = $this->createMock(PDOStatement::class);

        $this->standingsService = new StandingsService(
            $this->apiMock,
            $this->cacheMock,
            $this->pdoMock
        );
    }

    public function test_get_standings_returns_cached_data_when_cache_exists(): void
    {
        $cachedJson = json_encode(['standings' => [['table' => []]]]);

        $this->cacheMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($cachedJson);

        $this->apiMock
            ->expects($this->never())
            ->method('get');

        $result = $this->standingsService->getStandings();

        $this->assertIsArray($result);
    }

    public function test_get_standings_fetches_from_api_when_cache_is_empty(): void
    {
        $apiResponse = [
            'standings' => [
                [
                    'table' => [
                        [
                            'position'       => 1,
                            'team'           => ['id' => 57, 'name' => 'Arsenal'],
                            'playedGames'    => 30,
                            'won'            => 21,
                            'draw'           => 5,
                            'lost'           => 4,
                            'points'         => 68,
                            'goalsFor'       => 72,
                            'goalsAgainst'   => 30,
                            'goalDifference' => 42,
                            'form'           => 'WWDWW',
                        ]
                    ]
                ]
            ]
        ];

        $this->cacheMock
            ->expects($this->once())
            ->method('get')
            ->willReturn(null);

        $this->apiMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($apiResponse);

        $this->cacheMock
            ->expects($this->once())
            ->method('set');

        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->exactly(1))
            ->method('execute');

        $result = $this->standingsService->getStandings();

        $this->assertIsArray($result);
    }
}
