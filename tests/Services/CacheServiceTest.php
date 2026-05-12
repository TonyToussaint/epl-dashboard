<?php

namespace Tests\Services;

use Anthony\EplDashboard\Services\CacheService;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PDO;
use PDOStatement;

class CacheServiceTest extends TestCase
{
    private MockObject&PDO $pdoMock;
    private MockObject $stmtMock;
    private CacheService $cacheService;

    protected function setUp(): void
    {
        $this->stmtMock = $this->createMock(PDOStatement::class);
        $this->pdoMock  = $this->createMock(PDO::class);

        $this->cacheService = new CacheService($this->pdoMock);
    }

    public function test_get_returns_null_when_no_cache_exists(): void
    {
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute');

        $this->stmtMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $result = $this->cacheService->get('/competitions/PL/standings');

        $this->assertNull($result);
    }

    public function test_get_returns_cached_response_when_fresh_cache_exists(): void
    {
        $cachedJson = '{"standings": []}';

        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute');

        $this->stmtMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(['response_body' => $cachedJson]);

        $result = $this->cacheService->get('/competitions/PL/standings');

        $this->assertSame($cachedJson, $result);
    }

    public function test_set_saves_response_to_cache(): void
    {
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute');

        $this->cacheService->set('/competitions/PL/standings', '{"standings": []}');
    }
}
