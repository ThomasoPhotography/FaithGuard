<?php

/**
 * CSRF Token Generator with Biblical Character Encoding
 *
 * Generates secure CSRF tokens containing:
 * - Timestamp
 * - Optional database identifier
 * - Biblical reference encoding
 * - Random entropy
 *
 * Token format:
 * timestamp_id_biblical_reference_random
 *
 * Example:
 * 1720864000_0_23_a8b3c9d2
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
     *
     * Used during:
     * - Registration
     * - Login
     * - Forms before database records exist
     */
    public static function generate(): string
    {
        return self::generateToken(0);
    }

    /**
     * Generate token with database identifier
     *
     * @param int $databaseId
     * @param string|null $biblicalRef
     */
    public static function generateToken(
        int $databaseId,
        ?string $biblicalRef = null
    ): string {

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

        $timestamp = time();

        $idPortion = dechex($databaseId);

        $randomPortion = bin2hex(
            random_bytes(16)
        );

        return implode('_', [
            $timestamp,
            $idPortion,
            $biblicalChar,
            $randomPortion,
        ]);
    }

    /**
     * Extract biblical reference
     */
    public static function extractBiblicalRef(
        string $token
    ): ?string {

        $parts = explode('_', $token);

        if (count($parts) !== 4) {
            return null;
        }

        $index = hexdec($parts[2]);

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
        string $token
    ): ?int {

        $parts = explode('_', $token);

        if (count($parts) !== 4) {
            return null;
        }

        return hexdec($parts[1]);
    }

    /**
     * Extract timestamp
     */
    public static function extractTimestamp(
        string $token
    ): ?int {

        $parts = explode('_', $token);

        if (
            empty($parts[0]) ||
            ! is_numeric($parts[0])
        ) {
            return null;
        }

        return (int) $parts[0];
    }

    /**
     * Check token age
     */
    public static function isExpired(
        string $token,
        int $expirationSeconds = 3600
    ): bool {

        $timestamp = self::extractTimestamp($token);

        if (! $timestamp) {
            return true;
        }

        return (
            time() - $timestamp
        ) > $expirationSeconds;
    }

    /**
     * Validate token format
     */
    public static function isValidFormat(
        string $token
    ): bool {

        $parts = explode('_', $token);

        if (count($parts) !== 4) {
            return false;
        }

        // Timestamp
        if (
            ! is_numeric($parts[0]) ||
            strlen($parts[0]) < 10
        ) {
            return false;
        }

        // Database ID
        if (
            ! ctype_xdigit($parts[1])
        ) {
            return false;
        }

        // Biblical encoding
        if (
            ! ctype_xdigit($parts[2]) ||
            strlen($parts[2]) !== 2
        ) {
            return false;
        }

        // Random section
        if (
            ! ctype_xdigit($parts[3])
        ) {
            return false;
        }

        return true;
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
