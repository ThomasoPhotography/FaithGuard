<?php
session_start();
require_once __DIR__ . '/../../db/database.php';
header('Content-Type: application/json');

$debug = false;  // Set to true for debugging, then false for production

$data = json_decode(file_get_contents('php://input'), true);
if ($debug) {
    error_log('Decoded data: ' . print_r($data, true));
}

if (!$data || json_last_error() !== JSON_ERROR_NONE || !isset($data['email'], $data['password'])) {
    $response = ['error' => 'Invalid input'];
    if ($debug) {
        $response['debug'] = [
            'json_error' => json_last_error_msg(),
            'data' => $data
        ];
    }
    echo json_encode($response);
    exit;
}

// Sanitize inputs
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$password = $data['password'];

$user = Database::getSingleRow("SELECT * FROM users WHERE email = ?", [$email]);
if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['token'] = bin2hex(random_bytes(16));
    $_SESSION['logged_in'] = true;  // ADD THIS
    echo json_encode(['success' => true, 'token' => $_SESSION['token']]);
} else {
    echo json_encode(['error' => 'Invalid credentials']);
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">FaithGuard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                </ul>
            
            <div class="d-flex">
                <?php if ($is_logged_in): ?>
                    <span class="navbar-text me-3 text-success">
                        Logged in as: <strong><?php echo htmlspecialchars($username); ?></strong> 
                        (<?php echo htmlspecialchars($user_role); ?>)
                    </span>
                    <?php if ($user_role === 'admin'): ?>
                        <a href="/admin" class="btn btn-sm btn-warning me-2">Admin Panel</a>
                    <?php endif; ?>
                    <button class="btn btn-outline-danger js-logout-btn">Logout</button>
                <?php else: ?>
                    <button class="btn btn-outline-primary js-log">Login / Register</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>