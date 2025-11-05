<?php
require_once "../autoload.php";

header('Content-Type: application/json');

try {
    if (!SessionManager::isLoggedIn()) {
        echo json_encode([
            'success' => false,
            'message' => 'Not authenticated'
        ]);
        exit;
    }

    $db = (new Database())->connect();
    if (!$db) {
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed'
        ]);
        exit;
    }

    $userId = (int) SessionManager::get('user_id');

    $search = trim($_GET['q'] ?? '');
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $limit = max(1, min($limit, 50));

    $query = "SELECT id, name, muscle_group, equipment FROM exercises WHERE (is_default = true OR created_by_user_id = :userId)";
    $params = [':userId' => $userId];

    if ($search !== '') {
        $query .= " AND (name ILIKE :search OR muscle_group ILIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    $query .= " ORDER BY name ASC LIMIT :limit";

    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $paramType = ($key === ':userId') ? PDO::PARAM_INT : PDO::PARAM_STR;
        $stmt->bindValue($key, $value, $paramType);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    $exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $exercises
    ]);
} catch (PDOException $e) {
    error_log('searchExercises.php PDOException: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);
}
