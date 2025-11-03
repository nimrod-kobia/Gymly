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
    $splitName = $_POST['split_name'] ?? null;
    $description = $_POST['description'] ?? '';
    $trainingDays = (int)($_POST['training_days'] ?? 6);

    if (!$splitName) {
        echo json_encode(["success" => false, "message" => "Split name is required"]);
        exit;
    }

    $db = (new Database())->connect();
    if (!$db) {
        echo json_encode(["success" => false, "message" => "Database connection failed"]);
        exit;
    }

    // Start transaction
    $db->beginTransaction();

    // Create the split
    $stmt = $db->prepare("
        INSERT INTO workout_splits (user_id, split_name, split_type, description, is_active, created_at, updated_at)
        VALUES (:user_id, :name, 'custom', :description, false, NOW(), NOW())
    ");
    $stmt->execute([
        ':user_id' => $userId,
        ':name' => $splitName,
        ':description' => $description
    ]);
    $splitId = $db->lastInsertId();

    // Create placeholder days
    $dayNames = [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        7 => 'Sunday'
    ];

    for ($i = 1; $i <= $trainingDays; $i++) {
        $dayName = $dayNames[$i] ?? "Day $i";
        $stmt = $db->prepare("
            INSERT INTO split_days (split_id, day_name, day_of_week, is_rest_day, display_order, created_at, updated_at)
            VALUES (:split_id, :day_name, :day_of_week, false, :display_order, NOW(), NOW())
        ");
        $stmt->execute([
            ':split_id' => $splitId,
            ':day_name' => $dayName,
            ':day_of_week' => $i,
            ':display_order' => $i
        ]);
    }

    $db->commit();

    echo json_encode([
        "success" => true,
        "message" => "Split created successfully",
        "split_id" => $splitId
    ]);

} catch (PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log("createSplit.php PDOException: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>
