<?php
require_once "../autoload.php";

header('Content-Type: application/json');

try {
    // Check if user is logged in
    if (!SessionManager::isLoggedIn()) {
        echo json_encode(["success" => false, "message" => "Not authenticated"]);
        exit;
    }

    $splitId = $_GET['split_id'] ?? $_GET['id'] ?? null;
    
    if (!$splitId) {
        echo json_encode(["success" => false, "message" => "Split ID required"]);
        exit;
    }

    $db = (new Database())->connect();
    if (!$db) {
        echo json_encode(["success" => false, "message" => "Database connection failed"]);
        exit;
    }

    // Get split details
    $stmt = $db->prepare("SELECT * FROM workout_splits WHERE id = :id");
    $stmt->execute([':id' => $splitId]);
    $split = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$split) {
        echo json_encode(["success" => false, "message" => "Split not found"]);
        exit;
    }

    // Get split days with exercises
    $stmt = $db->prepare("
        SELECT sd.*, 
               COUNT(sde.id) as exercise_count
        FROM split_days sd
        LEFT JOIN split_day_exercises sde ON sd.id = sde.split_day_id
        WHERE sd.split_id = :split_id
        GROUP BY sd.id
        ORDER BY sd.display_order ASC
    ");
    $stmt->execute([':split_id' => $splitId]);
    $days = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get exercises for each day
    foreach ($days as &$day) {
        $stmt = $db->prepare("
            SELECT e.name, e.muscle_group, e.equipment, 
                   sde.target_sets, sde.target_reps, sde.target_rest_seconds
            FROM split_day_exercises sde
            JOIN exercises e ON sde.exercise_id = e.id
            WHERE sde.split_day_id = :day_id
            ORDER BY sde.display_order ASC
        ");
        $stmt->execute([':day_id' => $day['id']]);
        $day['exercises'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        "success" => true,
        "data" => [
            "split" => $split,
            "days" => $days
        ]
    ]);

} catch (PDOException $e) {
    error_log("getSplitDetails.php PDOException: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Database error"
    ]);
}
?>
