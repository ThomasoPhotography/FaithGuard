<?php
class RateLimiter
{
    private static string $lockFile = __DIR__ . '/../../db/.rate_limit.lock';

    private static function loadData(): array
    {
        if (! file_exists(self::$lockFile)) {
            return [];
        }
        $content = file_get_contents(self::$lockFile);
        return json_decode($content, true) ?? [];
    }

    private static function saveData(array $data): void
    {
        $dir = dirname(self::$lockFile);
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents(self::$lockFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    public static function isAllowed(string $ip, int $maxAttempts = 5, int $windowSeconds = 300): bool
    {
        $data = self::loadData();
        $key  = "login:$ip";

        if (isset($data[$key])) {
            [$count, $window] = $data[$key];
            // If the window has expired, the user is allowed
            if (time() - $window > $windowSeconds) {
                return true;
            }
            if ($count >= $maxAttempts) {
                return false;
            }
        }

        return true;
    }

    public static function registerFailure(string $ip, int $windowSeconds = 300): void
    {
        $data = self::loadData();
        $key  = "login:$ip";

        if (isset($data[$key])) {
            [$count, $window] = $data[$key];
            if (time() - $window > $windowSeconds) {
                // Window expired, start a new window with 1 failure
                $data[$key] = [1, time()];
            } else {
                // Within window, increment count
                $data[$key] = [$count + 1, $window];
            }
        } else {
            // First failure, start window
            $data[$key] = [1, time()];
        }

        self::saveData($data);
    }

    public static function reset(string $ip): void
    {
        $data = self::loadData();
        $key  = "login:$ip";
        if (isset($data[$key])) {
            unset($data[$key]);
            self::saveData($data);
        }
    }
}
