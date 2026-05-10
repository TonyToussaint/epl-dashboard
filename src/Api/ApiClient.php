<?php

namespace Anthony\EplDashboard\Api;

class ApiClient
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = $_ENV['API_BASE_URL'];
        $this->apiKey  = $_ENV['API_KEY'];
    }

    public function get(string $endpoint): array
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'X-Auth-Token: ' . $this->apiKey
            ],
        ]);

        $response   = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError  = curl_error($ch);

        if ($curlError) {
            throw new \RuntimeException("cURL error: $curlError");
        }

        if ($httpStatus !== 200) {
            throw new \RuntimeException("API error: HTTP $httpStatus for $url");
        }

        $decoded = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("JSON decode error: " . json_last_error_msg());
        }

        return $decoded;
    }
}
