<?php
require_once "../autoload.php";

header('Content-Type: application/json');

try {
    // Check if user is logged in
    if (!SessionManager::isLoggedIn()) {
        echo json_encode(["success" => false, "message" => "Not authenticated"]);
        exit;
    }

    $userId = SessionManager::getUserId();
    $type = $_GET['type'] ?? 'all'; // preset, custom, or all

    $db = (new Database())->connect();
    if (!$db) {
        echo json_encode(["success" => false, "message" => "Database connection failed"]);
        exit;
    }

    // Build query based on type
    $query = "SELECT * FROM workout_splits WHERE ";
    $params = [];
    
    if ($type === 'preset') {
        $query .= "split_type = :type AND user_id IS NULL";
        $params[':type'] = 'preset';
    } elseif ($type === 'custom') {
        $query .= "user_id = :user_id AND split_type = :type";
        $params[':user_id'] = $userId;
        $params[':type'] = 'custom';
    } else {
        $query .= "(user_id = :user_id OR user_id IS NULL)";
        $params[':user_id'] = $userId;
    }
    
    $query .= " ORDER BY split_type DESC, created_at DESC";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $splits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "splits" => $splits
    ]);

} catch (PDOException $e) {
    error_log("fetchWorkoutSplits.php PDOException: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Database error",
        "splits" => []
    ]);
}
?>
