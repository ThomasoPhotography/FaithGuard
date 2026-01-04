<?php
declare (strict_types = 1);

require_once __DIR__ . '/../config.php';

final class BibleApiService
{
    private static string $apiKey  = BIBLE_API_KEY;
    private static string $baseUrl = BIBLE_API_BASE_URL;

    public static function getBibles(): ?array
    {
        return self::callAPI('/bibles');
    }
    public static function getBibleVersion(string $bibleId): ?array
    {
        return self::callAPI("/bibles/{$bibleId}");
    }
    public static function getVerse(string $bibleId, string $verseId): ?array
    {
        return self::callAPI("/bibles/{$bibleId}/verses/{$verseId}");
    }
    public static function searchVerses(string $bibleId, string $query): ?array
    {
        return self::callAPI(
            "/bibles/{$bibleId}/search?query=" . urlencode($query)
        );
    }
    private static function callAPI(string $endpoint): ?array
    {
        $url = rtrim(self::$baseUrl, '/') . $endpoint;

        $context = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'header'  => [
                    'api-key: ' . self::$apiKey,
                    'Accept: application/json',
                    'User-Agent: FaithGuard/0.1.4',
                ],
                'timeout' => 10,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            $error = error_get_last();
            error_log('Bible API error: ' . ($error['message'] ?? 'Unknown error'));
            return null;
        }

        $data = json_decode($response, true);

        return is_array($data) ? $data : null;
    }
}
