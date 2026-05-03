<?php
    session_start();
    if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrf = $_SESSION['csrf_token'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Register - FaithGuard</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <main class="container my-5">
        <h1>Register</h1>
        <form class="c-form__register" action="#" method="post">
            <div class="mb-3">
                <label for="full_name" class="form-label">Full name</label>
                <input id="full_name" name="full_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" name="email" type="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" name="password" type="password" class="form-control" required>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">
            <div class="c-form__message mb-3"></div>
            <button class="btn c-btn c-btn--primary" type="submit">Create account</button>
        </form>
    </main>

    <script src="/assets/js/auth.js"></script>
</body>
</html>
