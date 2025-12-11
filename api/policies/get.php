<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../db/database.php";

$id  = $_GET['id'] ?? null;
$key = $_GET['key'] ?? null;

if (! $id && ! $key) {
    echo json_encode([
        "success" => false,
        "message" => "Missing parameter: id or key required.",
    ]);
    exit;
}

try {
    if ($id) {
        $policy = Database::getSingleRow(
            "SELECT * FROM policies WHERE id = ?",
            [$id]
        );
    } else {
        $policy = Database::getSingleRow(
            "SELECT * FROM policies WHERE policy_key = ?",
            [$key]
        );
    }

    echo json_encode([
        "success" => true,
        "policy"  => $policy,
    ]);

} catch (Exception $e) {
    error_log("POLICY_GET_ERROR: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Unable to fetch policy.",
    ]);
}
