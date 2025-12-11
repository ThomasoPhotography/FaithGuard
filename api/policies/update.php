<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../db/database.php";

$data = json_decode(file_get_contents("php://input"), true);

$id      = $data['id'] ?? null;
$title   = $data['title'] ?? null;
$content = $data['content'] ?? null;

if (! $id || ! $title || ! $content) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields.",
    ]);
    exit;
}

try {
    $rows = Database::execute(
        "UPDATE policies SET title = ?, content = ?, last_updated = NOW() WHERE id = ?",
        [$title, $content, $id]
    );

    echo json_encode([
        "success" => true,
        "message" => $rows > 0 ? "Policy updated." : "No changes applied.",
    ]);

} catch (Exception $e) {
    error_log("POLICY_UPDATE_ERROR: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Unable to update policy.",
    ]);
}
