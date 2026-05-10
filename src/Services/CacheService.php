<?php

namespace Anthony\EplDashboard\Services;

use PDO;

class CacheService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get(string $endpoint): ?string
    {
        $stmt = $this->pdo->prepare(
            'SELECT response_body FROM api_cache 
             WHERE endpoint = :endpoint 
             AND expires_at > NOW()'
        );

        $stmt->execute([':endpoint' => $endpoint]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $row['response_body'] : null;
    }

    public function set(string $endpoint, string $responseBody, int $ttlMinutes = 60): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO api_cache (endpoint, response_body, expires_at)
             VALUES (:endpoint, :response_body, NOW() + :ttl::INTERVAL)
             ON CONFLICT (endpoint) 
             DO UPDATE SET 
                response_body = EXCLUDED.response_body,
                fetched_at    = NOW(),
                expires_at    = NOW() + :ttl::INTERVAL'
        );

        $stmt->execute([
            ':endpoint'      => $endpoint,
            ':response_body' => $responseBody,
            ':ttl'           => $ttlMinutes . ' minutes',
        ]);
    }
}
