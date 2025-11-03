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
    $splitId = $_POST['split_id'] ?? null;

    if (!$splitId) {
        echo json_encode(["success" => false, "message" => "Split ID required"]);
        exit;
    }

    $db = (new Database())->connect();
    if (!$db) {
        echo json_encode(["success" => false, "message" => "Database connection failed"]);
        exit;
    }

    // Start transaction
    $db->beginTransaction();

    // Deactivate all current splits for this user
    $stmt = $db->prepare("
        UPDATE workout_splits 
        SET is_active = false 
        WHERE user_id = :user_id
    ");
    $stmt->execute([':user_id' => $userId]);

    // Check if split is preset (needs to be copied to user)
    $stmt = $db->prepare("SELECT * FROM workout_splits WHERE id = :id");
    $stmt->execute([':id' => $splitId]);
    $split = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$split) {
        $db->rollBack();
        echo json_encode(["success" => false, "message" => "Split not found"]);
        exit;
    }

    // If preset split, create a copy for the user
    if ($split['split_type'] === 'preset' && $split['user_id'] === null) {
        // Insert new split for user
        $timestamp = date('Y-m-d H:i:s');
        $stmt = $db->prepare("
            INSERT INTO workout_splits (user_id, split_name, split_type, description, is_active, created_at, updated_at)
            VALUES (:user_id, :name, 'custom', :description, true, :timestamp, :timestamp)
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':name' => $split['split_name'],
            ':description' => $split['description'],
            ':timestamp' => $timestamp
        ]);
        $newSplitId = $db->lastInsertId('workout_splits_id_seq');

        // Copy split days
        $stmt = $db->prepare("SELECT * FROM split_days WHERE split_id = :split_id ORDER BY display_order");
        $stmt->execute([':split_id' => $splitId]);
        $days = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($days as $day) {
            $stmt = $db->prepare("
                INSERT INTO split_days (split_id, day_name, day_of_week, is_rest_day, notes, display_order, created_at, updated_at)
                VALUES (:split_id, :day_name, :day_of_week, :is_rest_day, :notes, :display_order, :timestamp, :timestamp)
            ");
            $stmt->execute([
                ':split_id' => $newSplitId,
                ':day_name' => $day['day_name'],
                ':day_of_week' => $day['day_of_week'],
                ':is_rest_day' => filter_var($day['is_rest_day'], FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false',
                ':notes' => $day['notes'] ?? null,
                ':display_order' => $day['display_order'],
                ':timestamp' => $timestamp
            ]);
            $newDayId = $db->lastInsertId('split_days_id_seq');

            // Copy exercises for this day
            $stmt = $db->prepare("SELECT * FROM split_day_exercises WHERE split_day_id = :day_id ORDER BY display_order");
            $stmt->execute([':day_id' => $day['id']]);
            $exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($exercises as $exercise) {
                $stmt = $db->prepare("
                    INSERT INTO split_day_exercises (split_day_id, exercise_id, target_sets, target_reps, target_rest_seconds, notes, display_order, created_at, updated_at)
                    VALUES (:day_id, :exercise_id, :sets, :reps, :rest, :notes, :display_order, :timestamp, :timestamp)
                ");
                $stmt->execute([
                    ':day_id' => $newDayId,
                    ':exercise_id' => $exercise['exercise_id'],
                    ':sets' => $exercise['target_sets'],
                    ':reps' => $exercise['target_reps'],
                    ':rest' => $exercise['target_rest_seconds'],
                    ':notes' => $exercise['notes'],
                    ':display_order' => $exercise['display_order'],
                    ':timestamp' => $timestamp
                ]);
            }
        }
    } else {
        // Just activate the user's existing split
        $stmt = $db->prepare("
            UPDATE workout_splits 
            SET is_active = true 
            WHERE id = :id AND user_id = :user_id
        ");
        $stmt->execute([
            ':id' => $splitId,
            ':user_id' => $userId
        ]);
    }

    $db->commit();

    echo json_encode([
        "success" => true,
        "message" => "Split activated successfully"
    ]);

} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log("activateSplit.php PDOException: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log("activateSplit.php Exception: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}
?>
