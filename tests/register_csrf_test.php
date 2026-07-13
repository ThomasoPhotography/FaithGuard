<?php
require __DIR__ . '/../api/base.php';

function expectCondition(bool $condition, string $message): void
{
    if (! $condition) {
        throw new Exception($message);
    }
}

echo "=== CSRF Token Database Tests ===\n\n";

// Test 1: Generate CSRF token without user ID
echo "Test 1: Generate CSRF token (no user)\n";
$token1 = FaithGuardRepository::createCsrfToken(null, 3600);
expectCondition(! empty($token1), 'Token should be generated');
expectCondition(strlen($token1) === 64, 'Token should be 64 characters (32 bytes hex)');
echo "✓ Generated token: " . substr($token1, 0, 20) . "...\n\n";

// Test 2: Validate the newly created token
echo "Test 2: Validate newly created token\n";
$isValid = FaithGuardRepository::validateCsrfToken($token1);
expectCondition($isValid === true, 'Newly created token should be valid');
echo "✓ Token validation passed\n\n";

// Test 3: Validate invalid token
echo "Test 3: Validate invalid token\n";
$isInvalid = FaithGuardRepository::validateCsrfToken('invalid-token-xyz');
expectCondition($isInvalid === false, 'Invalid token should not validate');
echo "✓ Invalid token correctly rejected\n\n";

// Test 4: Validate empty token
echo "Test 4: Validate empty token\n";
$isEmpty = FaithGuardRepository::validateCsrfToken('');
expectCondition($isEmpty === false, 'Empty token should not validate');
echo "✓ Empty token correctly rejected\n\n";

// Test 5: Consume CSRF token
echo "Test 5: Consume CSRF token\n";
$token2     = FaithGuardRepository::createCsrfToken(null, 3600);
$isConsumed = FaithGuardRepository::consumeCsrfToken($token2);
expectCondition($isConsumed === true, 'Token should be consumed successfully');
echo "✓ Token consumed\n";

// Verify token is no longer valid after consumption
$isStillValid = FaithGuardRepository::validateCsrfToken($token2);
expectCondition($isStillValid === false, 'Consumed token should no longer be valid');
echo "✓ Consumed token is now invalid (prevents replay attacks)\n\n";

// Test 6: Check that getCsrfTokenFromRequest still works
echo "Test 6: getCsrfTokenFromRequest extraction\n";
$_SERVER['HTTP_X_CSRF_TOKEN'] = 'header-token-test';
$extracted                    = getCsrfTokenFromRequest([]);
expectCondition($extracted === 'header-token-test', 'Header-based CSRF token should be extracted');
echo "✓ Token extraction from headers works\n\n";

// Test 7: Token with user ID
echo "Test 7: Generate and validate CSRF token for specific user\n";
$testUserId = 9999; // Use a test user ID
$userToken  = FaithGuardRepository::createCsrfToken($testUserId, 3600);
expectCondition(! empty($userToken), 'User-specific token should be generated');
echo "✓ Generated user token: " . substr($userToken, 0, 20) . "...\n";

// Validate token with matching user ID
$isValidForUser = FaithGuardRepository::validateCsrfToken($userToken, $testUserId);
expectCondition($isValidForUser === true, 'Token should validate for correct user');
echo "✓ Token validates for correct user\n";

// Validate token with different user ID (should fail)
$isValidForOtherUser = FaithGuardRepository::validateCsrfToken($userToken, 5555);
expectCondition($isValidForOtherUser === false, 'Token should not validate for different user');
echo "✓ Token correctly rejected for different user\n\n";

// Test 8: Get user's CSRF tokens
echo "Test 8: Retrieve user's CSRF tokens\n";
$userToken1 = FaithGuardRepository::createCsrfToken($testUserId, 3600);
$userToken2 = FaithGuardRepository::createCsrfToken($testUserId, 3600);
sleep(1); // Small delay to ensure different timestamps
$userTokens = FaithGuardRepository::getUserCsrfTokens($testUserId);
expectCondition(is_array($userTokens), 'Should return array of tokens');
expectCondition(count($userTokens) >= 2, 'Should have at least 2 tokens for user');
echo "✓ Retrieved " . count($userTokens) . " tokens for user\n\n";

// Clean up expired tokens (there shouldn't be any since they're fresh)
echo "Test 9: Cleanup expired CSRF tokens\n";
// Create a token that expires immediately
$expiredToken = FaithGuardRepository::createCsrfToken(null, -1);
$cleanedCount = FaithGuardRepository::cleanupExpiredCsrfTokens();
expectCondition($cleanedCount >= 1, 'Should clean up at least the expired token we just created');
echo "✓ Cleaned up " . $cleanedCount . " expired tokens\n\n";

echo "=== All CSRF Tests Passed! ===\n";
echo "The CSRF token system is ready for v0.1.4-beta\n";
