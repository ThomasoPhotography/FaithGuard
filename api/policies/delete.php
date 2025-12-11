<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../db/database.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;

if (! $id) {
    echo json_encode([
        "success" => false,
        "message" => "Policy ID required.",
    ]);
    exit;
}

try {
    $rows = Database::execute(
        "DELETE FROM policies WHERE id = ?",
        [$id]
    );

    echo json_encode([
        "success" => $rows > 0,
        "message" => $rows > 0 ? "Policy deleted." : "Policy not found.",
    ]);

} catch (Exception $e) {
    error_log("POLICY_DELETE_ERROR: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Unable to delete policy.",
    ]);
}
