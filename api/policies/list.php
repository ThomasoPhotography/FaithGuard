<?php
header("Content-Type: application/json");

// Load database connection file
require_once __DIR__ . "/../../db/database.php";

try {
    // Fetch all policies (modify columns as needed)
    $policies = Database::getRows("
        SELECT id, policy_key, title, content, last_updated
        FROM policies
        ORDER BY last_updated DESC
    ");

    echo json_encode([
        "success"  => true,
        "policies" => $policies,
    ]);

} catch (Exception $e) {

    // Log the error server-side
    error_log("POLICY_LIST_ERROR: " . $e->getMessage());

    // Return safe JSON error message
    echo json_encode([
        "success" => false,
        "message" => "Unable to load policies at this time.",
    ]);
}
