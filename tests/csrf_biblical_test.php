<?php
require __DIR__ . '/../api/base.php';
require __DIR__ . '/../api/helper/CsrfTokenGenerator.php';

function expectCondition(bool $condition, string $message): void
{
    if (! $condition) {
        throw new Exception($message);
    }
}

echo "=== CSRF Token with Biblical Character Tests ===\n\n";

// Test 1: Token generator creates valid format
echo "Test 1: Generate token with biblical character encoding\n";
$testId = 12345;
$token  = CsrfTokenGenerator::generateToken($testId);
expectCondition(! empty($token), 'Token should be generated');
echo "✓ Generated token: $token\n";
echo "  Format: {timestamp}_{id}_{biblical_char}_{random}\n\n";

// Test 2: Extract ID from token
echo "Test 2: Extract database ID from token\n";
$extractedId = CsrfTokenGenerator::extractId($token);
expectCondition($extractedId === $testId, 'Extracted ID should match original ID');
echo "✓ Extracted ID: $extractedId (matches $testId)\n\n";

// Test 3: Extract biblical reference
echo "Test 3: Extract biblical character from token\n";
$biblicalRef = CsrfTokenGenerator::extractBiblicalRef($token);
expectCondition($biblicalRef !== null, 'Biblical reference should be extracted');
expectCondition(in_array($biblicalRef, CsrfTokenGenerator::getBiblicalReferences()), 'Biblical reference should be valid');
echo "✓ Extracted biblical reference: $biblicalRef\n\n";

// Test 4: Extract timestamp
echo "Test 4: Extract creation timestamp from token\n";
$timestamp = CsrfTokenGenerator::extractTimestamp($token);
$now       = time();
expectCondition($timestamp !== null, 'Timestamp should be extracted');
expectCondition(abs($timestamp - $now) < 5, 'Timestamp should be recent (within 5 seconds)');
echo "✓ Extracted timestamp: $timestamp (current: $now)\n\n";

// Test 5: Validate token format
echo "Test 5: Validate token format\n";
$isValid = CsrfTokenGenerator::isValidFormat($token);
expectCondition($isValid === true, 'Token should have valid format');
echo "✓ Token format is valid\n\n";

// Test 6: Test with specific biblical reference
echo "Test 6: Generate token with specific biblical reference\n";
$genesis    = CsrfTokenGenerator::generateToken(999, 'Genesis');
$genesisRef = CsrfTokenGenerator::extractBiblicalRef($genesis);
expectCondition($genesisRef === 'Genesis', 'Should extract Genesis from token');
echo "✓ Generated token with Genesis: $genesis\n";
echo "✓ Extracted reference: $genesisRef\n\n";

// Test 7: Test all biblical references are accessible
echo "Test 7: List all available biblical references\n";
$references = CsrfTokenGenerator::getBiblicalReferences();
expectCondition(count($references) === 56, 'Should have 56 biblical references');
echo "✓ Available biblical references: " . count($references) . "\n";
echo "  First 5: " . implode(', ', array_slice($references, 0, 5)) . "\n";
echo "  Last 5: " . implode(', ', array_slice($references, -5)) . "\n\n";

// Test 8: Database-backed CSRF token generation
echo "Test 8: Create CSRF token via FaithGuardRepository\n";
$dbToken = FaithGuardRepository::createCsrfToken(null, 3600);
expectCondition(! empty($dbToken), 'Database token should be created');
expectCondition(CsrfTokenGenerator::isValidFormat($dbToken), 'Database token should have valid format');
echo "✓ Created database token: $dbToken\n";

$dbBiblicalRef = FaithGuardRepository::getCsrfTokenBiblicalRef($dbToken);
echo "✓ Token biblical reference: $dbBiblicalRef\n";

$dbId = FaithGuardRepository::getCsrfTokenId($dbToken);
echo "✓ Token ID: $dbId\n";

$dbTimestamp = FaithGuardRepository::getCsrfTokenTimestamp($dbToken);
echo "✓ Token timestamp: $dbTimestamp\n\n";

// Test 9: Validate database token
echo "Test 9: Validate database-backed CSRF token\n";
$isValidDb = FaithGuardRepository::validateCsrfToken($dbToken);
expectCondition($isValidDb === true, 'Database token should validate');
echo "✓ Database token validation passed\n\n";

// Test 10: Consume and verify replay prevention
echo "Test 10: Consume token and prevent replay attacks\n";
$consumeToken = FaithGuardRepository::createCsrfToken(null, 3600);
$consumed     = FaithGuardRepository::consumeCsrfToken($consumeToken);
expectCondition($consumed === true, 'Token should be consumed');
$isStillValid = FaithGuardRepository::validateCsrfToken($consumeToken);
expectCondition($isStillValid === false, 'Consumed token should no longer be valid');
echo "✓ Token consumed successfully\n";
echo "✓ Replay attacks prevented (consumed token rejected)\n\n";

// Test 11: Token format variations
echo "Test 11: Test invalid token formats\n";
$invalidTokens = [
    'invalid',
    '123_abc',
    '1720864000_12345_gg_invalid',
    '1720864000_12345_f_',
];
foreach ($invalidTokens as $invalidToken) {
    $isInvalid = ! CsrfTokenGenerator::isValidFormat($invalidToken);
    expectCondition($isInvalid, "Token '$invalidToken' should be invalid");
}
echo "✓ All invalid tokens correctly rejected\n\n";

echo "=== All Biblical CSRF Tests Passed! ===\n";
echo "✅ CSRF tokens now include hidden biblical characters!\n";
echo "Token Format: {timestamp}_{database_id}_{biblical_char_hex}_{random_hex}\n";
echo "Example: 1720864000_2d_0f_a8b3c9d2\n";
echo "  - 1720864000 = Creation timestamp\n";
echo "  - 2d = Database ID (45 in decimal)\n";
echo "  - 0f = Biblical character (15 = Psalms)\n";
echo "  - a8b3c9d2 = Random entropy for security\n";
echo "Biblical characters are automatically encoded and can be extracted.\n";
echo "Ready for v0.1.4-beta with spiritual identity!\n";
