<?php
// CSRF Token Generator with Biblical Character Encoding
// Generates unique CSRF tokens that:
// Use database ID as base identifier
// Encode a hidden biblical character/reference
// Include randomness for security
// Are automatically generated
class CsrfTokenGenerator
{
    // Biblical books/references for encoding
    // Index corresponds to value used in token encoding
    private static array $biblicalReferences = [
        'Genesis',       // 0
        'Exodus',        // 1
        'Leviticus',     // 2
        'Numbers',       // 3
        'Deuteronomy',   // 4
        'Joshua',        // 5
        'Judges',        // 6
        'Ruth',          // 7
        'Samuel',        // 8
        'Kings',         // 9
        'Chronicles',    // 10
        'Ezra',          // 11
        'Nehemiah',      // 12
        'Esther',        // 13
        'Job',           // 14
        'Psalms',        // 15
        'Proverbs',      // 16
        'Ecclesiastes',  // 17
        'Isaiah',        // 18
        'Jeremiah',      // 19
        'Lamentations',  // 20
        'Ezekiel',       // 21
        'Daniel',        // 22
        'Hosea',         // 23
        'Joel',          // 24
        'Amos',          // 25
        'Obadiah',       // 26
        'Jonah',         // 27
        'Micah',         // 28
        'Nahum',         // 29
        'Habakkuk',      // 30
        'Zephaniah',     // 31
        'Haggai',        // 32
        'Zechariah',     // 33
        'Malachi',       // 34
        'Matthew',       // 35
        'Mark',          // 36
        'Luke',          // 37
        'John',          // 38
        'Acts',          // 39
        'Romans',        // 40
        'Corinthians',   // 41
        'Galatians',     // 42
        'Ephesians',     // 43
        'Philippians',   // 44
        'Colossians',    // 45
        'Thessalonians', // 46
        'Timothy',       // 47
        'Titus',         // 48
        'Philemon',      // 49
        'Hebrews',       // 50
        'James',         // 51
        'Peter',         // 52
        'John',          // 53
        'Jude',          // 54
        'Revelation',    // 55
    ];

    // Generate a CSRF token with encoded biblical character
    // Format: {timestamp}_{id}_{biblical_char}_{random}
    // Example: 1720864000_12345_f_a8b3c9d2
    // @param int $databaseId The database record ID (auto-increment)
    // @param string|null $biblicalRef Optional biblical reference to encode (defaults to random)
    // @return string Encoded CSRF token
    public static function generateToken(int $databaseId, ?string $biblicalRef = null): string
    {
        // Get biblical character/index
        if ($biblicalRef === null) {
            $biblicalIndex = random_int(0, count(self::$biblicalReferences) - 1);
        } else {
            $biblicalIndex = self::getBiblicalIndex($biblicalRef);
        }

        // Encode biblical index as hex (single byte)
        $biblicalChar = dechex($biblicalIndex);
        if (strlen($biblicalChar) === 1) {
            $biblicalChar = '0' . $biblicalChar; // Pad to 2 chars
        }

        // Current timestamp for temporal uniqueness
        $timestamp = time();

        // ID portion (variable length based on ID size)
        $idPortion = dechex($databaseId);

        // Random 16-character hex string for additional entropy
        $randomPortion = bin2hex(random_bytes(8));

        // Combine: timestamp_id_biblicalChar_random
        $token = $timestamp . '_' . $idPortion . '_' . $biblicalChar . '_' . $randomPortion;

        return $token;
    }

    // Extract the biblical reference from a CSRF token
    // @param string $token The CSRF token
    // @return string|null The biblical reference, or null if invalid token
    public static function extractBiblicalRef(string $token): ?string
    {
        // Token format: timestamp_id_biblicalChar_random
        $parts = explode('_', $token);

        if (count($parts) < 3) {
            return null;
        }

        $biblicalCharHex = $parts[2];

        try {
            $biblicalIndex = hexdec($biblicalCharHex);

            if ($biblicalIndex < 0 || $biblicalIndex >= count(self::$biblicalReferences)) {
                return null;
            }

            return self::$biblicalReferences[$biblicalIndex];
        } catch (\Throwable $e) {
            return null;
        }
    }

    // Extract the database ID from a CSRF token
    // @param string $token The CSRF token
    // @return int|null The database ID, or null if invalid token
    public static function extractId(string $token): ?int
    {
        $parts = explode('_', $token);

        if (count($parts) < 2) {
            return null;
        }

        try {
            return (int) hexdec($parts[1]);
        } catch (\Throwable $e) {
            return null;
        }
    }

    // Extract the timestamp from a CSRF token
    // @param string $token The CSRF token
    // @return int|null The Unix timestamp, or null if invalid token
    public static function extractTimestamp(string $token): ?int
    {
        $parts = explode('_', $token);

        if (empty($parts[0])) {
            return null;
        }

        try {
            return (int) $parts[0];
        } catch (\Throwable $e) {
            return null;
        }
    }

    // Get the index of a biblical reference
    // @param string $ref The biblical reference name
    // @return int The index (0-55), or -1 if not found
    public static function getBiblicalIndex(string $ref): int
    {
        $index = array_search(trim($ref), self::$biblicalReferences);
        return $index !== false ? $index : -1;
    }

    //Get all available biblical references
    // @return array Array of biblical reference names
    public static function getBiblicalReferences(): array
    {
        return self::$biblicalReferences;
    }

    // Validate token format (basic structure check)
    // @param string $token The CSRF token
    // @return bool True if token has valid structure
    public static function isValidFormat(string $token): bool
    {
        $parts = explode('_', $token);

        if (count($parts) !== 4) {
            return false;
        }

        // Check timestamp is numeric
        if (! is_numeric($parts[0]) || strlen($parts[0]) < 10) {
            return false;
        }

        // Check ID portion is hex
        if (! ctype_xdigit($parts[1])) {
            return false;
        }

        // Check biblical char is hex and 2 chars
        if (! ctype_xdigit($parts[2]) || strlen($parts[2]) !== 2) {
            return false;
        }

        // Check random portion is hex
        if (! ctype_xdigit($parts[3])) {
            return false;
        }

        return true;
    }
}
