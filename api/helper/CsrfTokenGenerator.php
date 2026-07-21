<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "CSRF START<br>";

/**
 * CSRF Token Generator with Biblical Character Encoding
 *
 * Generates secure CSRF tokens containing:
 * - Optional database identifier
 * - Biblical reference encoding
 * - Random entropy
 *
 * Token format:
 * database_id_biblical_reference_random
 *
 * Example:
 * 0_23_a8b3c9d2...
 */

class CsrfTokenGenerator
{
    /**
     * Biblical references used for token encoding
     */
    private static array $biblicalReferences = [
        'Genesis',
        'Exodus',
        'Leviticus',
        'Numbers',
        'Deuteronomy',
        'Joshua',
        'Judges',
        'Ruth',
        'Samuel',
        'Kings',
        'Chronicles',
        'Ezra',
        'Nehemiah',
        'Esther',
        'Job',
        'Psalms',
        'Proverbs',
        'Ecclesiastes',
        'Isaiah',
        'Jeremiah',
        'Lamentations',
        'Ezekiel',
        'Daniel',
        'Hosea',
        'Joel',
        'Amos',
        'Obadiah',
        'Jonah',
        'Micah',
        'Nahum',
        'Habakkuk',
        'Zephaniah',
        'Haggai',
        'Zechariah',
        'Malachi',

        'Matthew',
        'Mark',
        'Luke',
        'John_Gospel',
        'Acts',
        'Romans',
        'Corinthians',
        'Galatians',
        'Ephesians',
        'Philippians',
        'Colossians',
        'Thessalonians',
        'Timothy',
        'Titus',
        'Philemon',
        'Hebrews',
        'James',
        'Peter',
        'John_Letter',
        'Jude',
        'Revelation',
    ];

    /**
     * Generate a standard CSRF token
     */
    public static function generate(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Generate token with database identifier
     *
     * @param int $databaseId
     * @param string|null $biblicalRef
     */
    public static function generateToken(
        int $databaseId,
        ?string $biblicalRef = null): string {

        if ($biblicalRef === null) {

            $biblicalIndex = random_int(
                0,
                count(self::$biblicalReferences) - 1
            );

        } else {

            $biblicalIndex = self::getBiblicalIndex($biblicalRef);

            if ($biblicalIndex < 0) {
                $biblicalIndex = 0;
            }
        }

        // Encode biblical reference
        $biblicalChar = dechex($biblicalIndex);

        if (strlen($biblicalChar) === 1) {
            $biblicalChar = '0' . $biblicalChar;
        }

        // Encode database identifier
        $idPortion = dechex($databaseId);

        // Secure random entropy
        $randomPortion = bin2hex(
            random_bytes(32)
        );

        return implode('_', [
            $idPortion,
            $biblicalChar,
            $randomPortion,
        ]);
    }

    /**
     * Extract biblical reference
     */
    public static function extractBiblicalRef(
        string $token): ?string {

        $parts = explode('_', $token);

        if (count($parts) !== 3) {
            return null;
        }

        $index = hexdec($parts[1]);

        if (
            $index < 0 ||
            $index >= count(self::$biblicalReferences)
        ) {
            return null;
        }

        return self::$biblicalReferences[$index];
    }

    /**
     * Extract database ID
     */
    public static function extractId(
        string $token): ?int {

        $parts = explode('_', $token);

        if (count($parts) !== 3) {
            return null;
        }

        return hexdec($parts[0]);
    }

    /**
     * Validate token format
     */
    public static function isValidFormat(string $token): bool
    {
        return preg_match('/^[a-f0-9]{64}$/', $token) === 1;
    }

    /**
     * Get biblical index
     */
    public static function getBiblicalIndex(
        string $reference
    ): int {

        $index = array_search(
            trim($reference),
            self::$biblicalReferences,
            true
        );

        return $index === false
            ? -1
            : $index;
    }

    /**
     * Get all references
     */
    public static function getBiblicalReferences(): array
    {
        return self::$biblicalReferences;
    }
}
