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

    $db = (new Database())->connect();
    if (!$db) {
        echo json_encode(["success" => false, "message" => "Database connection failed"]);
        exit;
    }

    // Get user's active split
    $stmt = $db->prepare("
        SELECT * FROM workout_splits 
        WHERE user_id = :user_id AND is_active = true
        LIMIT 1
    ");
    $stmt->execute([':user_id' => $userId]);
    $activeSplit = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($activeSplit) {
        echo json_encode([
            "success" => true,
            "data" => $activeSplit
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "No active split",
            "data" => null
        ]);
    }

} catch (PDOException $e) {
    error_log("getActiveSplit.php PDOException: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Database error"
    ]);
}
?>
