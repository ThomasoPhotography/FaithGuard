<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../db/database.php";

$data = json_decode(file_get_contents("php://input"), true);

$policy_key = $data['policy_key'] ?? null;
$title      = $data['title'] ?? null;
$content    = $data['content'] ?? null;

if (! $policy_key || ! $title || ! $content) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields.",
    ]);
    exit;
}

try {
    $rows = Database::execute(
        "INSERT INTO policies (policy_key, title, content) VALUES (?, ?, ?)",
        [$policy_key, $title, $content]
    );

    echo json_encode([
        "success" => $rows > 0,
        "message" => $rows > 0 ? "Policy created." : "No changes applied.",
    ]);

} catch (Exception $e) {
    error_log("POLICY_CREATE_ERROR: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Unable to create policy.",
    ]);
}
