<?php
require_once "../autoload.php";

header('Content-Type: application/json');

try {
    $db = (new Database())->connect();
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    $stmt = $db->query('SELECT id, name FROM companies ORDER BY name ASC LIMIT 500');

    echo json_encode([
        'success' => true,
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
