<?php
    // Simple admin UI for editing policies
    require_once __DIR__ . '/../db/database.php';
    require_once __DIR__ . '/../db/FaithGuardRepository.php';

    session_start();
    // TODO: Replace this with real auth check; keep simple guard
    if (empty($_SESSION['user_id'])) {
    header('Location: /dashboard.php');
    exit;
    }

    $type  = $_GET['type'] ?? '';
    $valid = ['terms', 'privacy', 'community', 'cookie', 'terms'];
    if (! in_array($type, $valid, true)) {
    $type = 'privacy';
    }

    $policy = FaithGuardRepository::getPolicyContent($type) ?: ['content_title' => '', 'content_text' => ''];

    if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Policy - Admin</title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body class="container">
    <h1>Edit Policy: <?php echo htmlspecialchars($type); ?></h1>
    <form id="policyForm">
        <input type="hidden" name="slug" value="<?php echo htmlspecialchars($type); ?>">
        <div>
            <label>Title</label>
            <input type="text" name="content_title" value="<?php echo htmlspecialchars($policy['content_title'] ?? ''); ?>" style="width:100%">
        </div>
        <div>
            <label>Content</label>
            <textarea name="content_text" rows="12" style="width:100%"><?php echo htmlspecialchars($policy['content_text'] ?? ''); ?></textarea>
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <button type="submit">Save</button>
    </form>

    <script>
    document.getElementById('policyForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = e.target;
        const data = {
            slug: form.slug.value,
            content_title: form.content_title.value,
            content_text: form.content_text.value,
            csrf_token: form.csrf_token.value
        };
        const res = await fetch('/api/admin/edit-policy.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const j = await res.json();
        if (j.success) {
            alert(j.message || 'Saved');
        } else {
            alert('Error: ' + (j.error || 'Unknown'));
        }
    });
    </script>
</body>
</html>
