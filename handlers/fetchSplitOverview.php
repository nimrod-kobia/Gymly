<?php
require_once "../autoload.php";

header('Content-Type: application/json');

try {
    if (!SessionManager::isLoggedIn()) {
        echo json_encode([
            "success" => false,
            "message" => "Not authenticated"
        ]);
        exit;
    }

    $userId = SessionManager::getUserId();
    $db = (new Database())->connect();

    if (!$db) {
        echo json_encode([
            "success" => false,
            "message" => "Database connection failed"
        ]);
        exit;
    }

    // Fetch active split
    $activeSplit = null;
    $stmt = $db->prepare("
        SELECT id, split_name, split_type, description, is_active, created_at, updated_at
        FROM workout_splits
        WHERE user_id = :user_id AND is_active = true
        ORDER BY updated_at DESC
        LIMIT 1
    ");
    $stmt->execute([':user_id' => $userId]);
    $activeSplit = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

    // Fetch preset splits
    $stmt = $db->query("
        SELECT id, split_name, split_type, description, is_active, created_at, updated_at
        FROM workout_splits
        WHERE split_type = 'preset' AND user_id IS NULL
        ORDER BY created_at DESC
    ");
    $presetSplits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch user's custom splits
    $stmt = $db->prepare("
        SELECT id, split_name, split_type, description, is_active, created_at, updated_at
        FROM workout_splits
        WHERE user_id = :user_id AND split_type = 'custom'
        ORDER BY is_active DESC, updated_at DESC
    ");
    $stmt->execute([':user_id' => $userId]);
    $customSplits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => [
            "active_split" => $activeSplit,
            "preset_splits" => $presetSplits,
            "custom_splits" => $customSplits
        ]
    ]);
} catch (PDOException $e) {
    error_log("fetchSplitOverview.php PDOException: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Database error"
    ]);
}
?>
