<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

    $userId = (int) SessionManager::getUserId();

    $search = trim($_GET['q'] ?? '');
    $muscleGroup = trim($_GET['muscle_group'] ?? '');
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $limit = max(1, min($limit, 50));

    $query = "SELECT id, name, muscle_group, equipment FROM exercises WHERE (is_default = true OR created_by_user_id = :userId)";
    $params = [':userId' => $userId];

    if ($muscleGroup !== '') {
        $query .= " AND muscle_group = :muscle_group";
        $params[':muscle_group'] = $muscleGroup;
    }

    if ($search !== '') {
        $query .= " AND (name ILIKE :search)";
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
} catch (Exception $e) {
    error_log('searchExercises.php Exception: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
