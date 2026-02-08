<?php

class BibleService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $config = require __DIR__ . '/../api/config.php';
        $this->baseUrl = rtrim($config['bible_base_url'], '/');
        $this->apiKey  = $config['bible_api_key'];
    }

    public function fetchVerse(string $reference, string $version): ?array
    {
        $url = $this->baseUrl . "/bibles/{$version}/verses/" . urlencode($reference);

        $context = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'header'  => [
                    "api-key: {$this->apiKey}",
                    "Accept: application/json"
                ]
            ]
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true);

        if (!isset($data['data'])) {
            return null;
        }

        return [
            'reference' => $data['data']['reference'] ?? '',
            'text'      => strip_tags($data['data']['content'] ?? ''),
            'version'   => $version
        ];
    }
}