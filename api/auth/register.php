<?php
session_start();
require_once __DIR__ . '/../../db/database.php';
require_once __DIR__ . '/../../db/FaithGuardRepository.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $name = trim($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'All fields are required.']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Invalid email format.']);
        exit;
    }
    if (strlen($password) < 8) {
        echo json_encode(['success' => false, 'error' => 'Password must be at least 8 characters.']);
        exit;
    }

    // Check for existing user
    $existing_user = Database::getSingleRow("SELECT id FROM users WHERE email = ?", [$email]);
    if ($existing_user) {
        echo json_encode(['success' => false, 'error' => 'User already exists.']);
        exit;
    }

    // Hash password and insert
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $result = Database::execute("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'user')", [$name, $email, $hashed_password]);

    if ($result > 0) {
        $new_user = Database::getSingleRow("SELECT id FROM users WHERE email = ?", [$email]);
        if ($new_user) {
            $_SESSION = [];
            $_SESSION['user_id'] = $new_user['id'];
            $_SESSION['logged_in'] = true;
            session_regenerate_id(true);
            echo json_encode(['success' => true]);
        } else {
            error_log('Registration: User inserted but not found for session setup.');
            echo json_encode(['success' => false, 'error' => 'Registration succeeded, but failed to log in.']);
        }
    } else {
        error_log('Registration: Database insert failed for email: ' . $email);
        echo json_encode(['success' => false, 'error' => 'Database insert failed.']);
    }
    exit;
}

// Display HTML page with modal
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FaithGuard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Welcome to FaithGuard</h2>
        <p>Please register to continue.</p>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">Register</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="registerForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </form>
                    <div id="message" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>