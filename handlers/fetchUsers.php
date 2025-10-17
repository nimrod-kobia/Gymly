<?php
require_once "../autoload.php";

header('Content-Type: application/json');

try {
    $db = (new Database())->connect();
    if (!$db) {
        echo json_encode(["data" => []]);
        exit;
    }

    $stmt = $db->query("
        SELECT id, full_name, username, email, created_at, updated_at, is_verified
        FROM users
        ORDER BY id ASC
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["data" => $users]);
} catch (PDOException $e) {
    echo json_encode(["data" => [], "error" => $e->getMessage()]);
}
