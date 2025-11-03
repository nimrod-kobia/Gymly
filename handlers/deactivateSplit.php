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

    // Deactivate all splits for this user
    $stmt = $db->prepare("
        UPDATE workout_splits 
        SET is_active = false 
        WHERE user_id = :user_id
    ");
    $stmt->execute([':user_id' => $userId]);

    echo json_encode([
        "success" => true,
        "message" => "Split deactivated successfully"
    ]);

} catch (PDOException $e) {
    error_log("deactivateSplit.php PDOException: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Database error"
    ]);
}
?>
