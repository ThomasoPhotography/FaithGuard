# CSRF Token System with Biblical Characters

## Overview

FaithGuard implements a sophisticated CSRF (Cross-Site Request Forgery) token system that combines security with spiritual identity through embedded biblical character encoding.

## Token Structure

CSRF tokens follow this format:

```
{timestamp}_{database_id}_{biblical_char_hex}_{random_hex}
```

### Example Token
```
1720864000_2d_0f_a8b3c9d2
├─ 1720864000  = Creation timestamp (Unix seconds)
├─ 2d           = Database ID in hex (45 in decimal)
├─ 0f           = Biblical character hex (15 = Psalms)
└─ a8b3c9d2    = Random 16-char hex for entropy
```

## Components

### 1. **Timestamp** (10+ characters)
- Unix timestamp of token creation
- Allows verification of token age

### 2. **Database ID** (Variable length)
- Hexadecimal representation of the auto-increment token ID
- Serves as unique identifier
- Enables token tracking in the database

### 3. **Biblical Character** (2-character hex)
- Encodes one of 56 biblical books
- Range: `00` (Genesis) to `37` (Revelation)
- Provides spiritual identity to each token
- Extractable for audit/logging purposes

### 4. **Random Entropy** (8 bytes / 16 hex characters)
- High-entropy random string
- Prevents token prediction
- Ensures security against brute-force attacks

## Biblical Character Mapping

| Index | Book | Index | Book | Index | Book |
|-------|------|-------|------|-------|------|
| 0x00 | Genesis | 0x13 | Esther | 0x26 | Obadiah |
| 0x01 | Exodus | 0x14 | Job | 0x27 | Jonah |
| 0x02 | Leviticus | 0x15 | Psalms | 0x28 | Micah |
| 0x03 | Numbers | 0x16 | Proverbs | 0x29 | Nahum |
| 0x04 | Deuteronomy | 0x17 | Ecclesiastes | 0x2a | Habakkuk |
| 0x05 | Joshua | 0x18 | Isaiah | 0x2b | Zephaniah |
| 0x06 | Judges | 0x19 | Jeremiah | 0x2c | Haggai |
| 0x07 | Ruth | 0x1a | Lamentations | 0x2d | Zechariah |
| 0x08 | Samuel | 0x1b | Ezekiel | 0x2e | Malachi |
| 0x09 | Kings | 0x1c | Daniel | 0x2f | Matthew |
| 0x0a | Chronicles | 0x1d | Hosea | 0x30 | Mark |
| 0x0b | Ezra | 0x1e | Joel | 0x31 | Luke |
| 0x0c | Nehemiah | 0x1f | Amos | 0x32 | John |
| 0x0d | Esther | 0x20 | Obadiah | 0x33 | Acts |
| 0x0e | Job | 0x21 | Jonah | 0x34 | Romans |
| 0x0f | Psalms | 0x22 | Micah | 0x35 | Corinthians |
| 0x10 | Proverbs | 0x23 | Nahum | 0x36 | Galatians |
| 0x11 | Ecclesiastes | 0x24 | Habakkuk | 0x37 | Ephesians |
| 0x12 | Isaiah | 0x25 | Zephaniah | ... | ... |

*Full mapping available in `CsrfTokenGenerator::$biblicalReferences`*

## API Usage

### Generating Tokens

#### Random Biblical Character (Automatic)
```php
$token = FaithGuardRepository::createCsrfToken(null, 3600);
// Result: "1720864000_2d_0f_a8b3c9d2"
```

#### Specific Biblical Reference
```php
$token = FaithGuardRepository::createCsrfToken(null, 3600, 'Genesis');
// Result: "1720864000_2e_00_b9c2d1e3" (always has biblical char 00)
```

#### User-Specific Token
```php
$userId = 42;
$token = FaithGuardRepository::createCsrfToken($userId, 3600, 'John');
// Creates token for user 42 with John as the biblical reference
```

### Validating Tokens

```php
// Check if token is valid and not expired
if (FaithGuardRepository::validateCsrfToken($token)) {
    // Token is valid
}

// Validate token for specific user
if (FaithGuardRepository::validateCsrfToken($token, $userId)) {
    // Token is valid for this user
}
```

### Extracting Information

```php
// Get the embedded biblical reference
$biblical = FaithGuardRepository::getCsrfTokenBiblicalRef($token);
echo $biblical; // e.g., "Psalms"

// Get the database ID
$id = FaithGuardRepository::getCsrfTokenId($token);
echo $id; // e.g., 45

// Get the creation timestamp
$timestamp = FaithGuardRepository::getCsrfTokenTimestamp($token);
echo date('Y-m-d H:i:s', $timestamp);

// Check token format validity
if (FaithGuardRepository::isCsrfTokenFormatValid($token)) {
    // Token structure is correct
}
```

### Consuming Tokens

```php
// Consume token (delete after use) - prevents replay attacks
if (FaithGuardRepository::consumeCsrfToken($token)) {
    // Token successfully consumed
}

// After consumption, token cannot be reused
FaithGuardRepository::validateCsrfToken($token); // Returns false
```

### Maintenance

```php
// Clean up expired tokens (call periodically)
$deleted = FaithGuardRepository::cleanupExpiredCsrfTokens();
echo "Deleted $deleted expired tokens";

// Get all valid tokens for a user
$tokens = FaithGuardRepository::getUserCsrfTokens($userId);
```

## Database Schema

```sql
CREATE TABLE `csrf_tokens` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int DEFAULT NULL,
  `token` varchar(255) NOT NULL UNIQUE,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  
  KEY `user_id` (`user_id`),
  KEY `idx_expires` (`expires_at`),
  CONSTRAINT `csrf_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
)
```

## Registration Flow

### 1. **GET /api/auth/register.php**
Returns registration modal HTML with embedded CSRF token

```javascript
// Frontend request
fetch('/api/auth/register.php', {
    method: 'GET',
    headers: { 'Content-Type': 'application/json' }
})
.then(r => r.text())
.then(html => {
    // Modal contains hidden input with CSRF token
    // Token format: {timestamp}_{id}_{biblical}_{random}
});
```

### 2. **POST /api/auth/register.php**
Submits registration data with CSRF token

```javascript
// Frontend submission
fetch('/api/auth/register.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        first_name: 'John',
        last_name: 'Doe',
        email: 'john@example.com',
        password: 'SecurePassword123',
        csrf_token: '1720864000_2d_0f_a8b3c9d2'
    })
});
```

### 3. **Token Validation & Consumption**
- Token validated against database
- Checked for expiration (1 hour)
- Consumed (deleted) after successful registration
- Prevents replay attacks and form resubmission

## Security Features

✅ **Database-backed** - Tokens stored in database, not in session
✅ **Unique per request** - Each token is unique with high entropy random component
✅ **Time-limited** - Tokens expire after 1 hour
✅ **One-time use** - Tokens consumed after use prevent replay attacks
✅ **User-scoped** - Optional user association for per-user token validation
✅ **Format validation** - Strict format checking prevents token forgery
✅ **Extractable metadata** - Biblical reference, ID, and timestamp are extractable for audit trails

## Spiritual Integration

Each CSRF token carries a hidden biblical reference, connecting security practices to scripture:

- **Random selection** provides variety and unpredictability
- **Specific reference** allows intentional spiritual association (e.g., "John" for John 3:16)
- **Auditable** - Scripture references can be logged for spiritual accountability
- **Meaningful** - Connects technical security measures to faith journey

## Testing

Comprehensive test suite available in `tests/csrf_biblical_test.php`:

```bash
php tests/csrf_biblical_test.php
```

Tests cover:
- Token generation with encoding
- ID extraction
- Biblical reference extraction
- Timestamp extraction
- Format validation
- Database integration
- Token consumption
- Replay prevention
- Invalid token rejection

## Best Practices

1. **Always consume tokens** after successful form submission
2. **Regenerate on privilege escalation** (login, role change)
3. **Clean up expired tokens** periodically (via cron job)
4. **Validate format** before database lookup
5. **Log biblical references** for audit trails
6. **Use per-user tokens** for sensitive operations
7. **Set appropriate expiration** based on use case (1 hour default)

## Future Enhancements

- [ ] Token rotation policy (auto-refresh)
- [ ] IP-based validation
- [ ] User-agent validation
- [ ] Rate limiting per IP
- [ ] Audit logging dashboard
- [ ] Biblical reference preferences per user
- [ ] Token analytics (most common references, usage patterns)

---

**Ready for v0.1.4-beta!** 🙏
