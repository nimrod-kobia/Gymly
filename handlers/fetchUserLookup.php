<?php
require_once "../autoload.php";

header('Content-Type: application/json');

try {
    SessionManager::requireAuth();

    $query = trim($_GET['q'] ?? '');
    if ($query === '') {
        echo json_encode(['success' => true, 'data' => []]);
        exit;
    }

    $db = (new Database())->connect();
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    $stmt = $db->prepare(
        'SELECT id, username, email
         FROM users
         WHERE username ILIKE :search OR email ILIKE :search
         ORDER BY username ASC
         LIMIT 20'
    );

    $stmt->execute([':search' => '%' . $query . '%']);

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
