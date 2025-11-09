<?php
require_once "../autoload.php";

header('Content-Type: application/json');

try {
    if (!SessionManager::isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }

    $db = (new Database())->connect();
    $userId = (int) SessionManager::getUserId();
    
    $splitDayId = isset($_POST['split_day_id']) ? (int)$_POST['split_day_id'] : 0;
    $exerciseId = isset($_POST['exercise_id']) ? (int)$_POST['exercise_id'] : 0;
    $completed = isset($_POST['completed']) ? (bool)$_POST['completed'] : false;
    $completionDate = isset($_POST['completion_date']) ? $_POST['completion_date'] : date('Y-m-d');

    if (!$splitDayId || !$exerciseId) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }

    // Check if completion record exists
    $stmt = $db->prepare("
        SELECT id FROM exercise_completions 
        WHERE user_id = :user_id 
        AND split_day_id = :split_day_id 
        AND exercise_id = :exercise_id 
        AND completion_date = :completion_date
    ");
    $stmt->execute([
        ':user_id' => $userId,
        ':split_day_id' => $splitDayId,
        ':exercise_id' => $exerciseId,
        ':completion_date' => $completionDate
    ]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Update existing record
        $stmt = $db->prepare("
            UPDATE exercise_completions 
            SET completed = :completed,
                completed_at = :completed_at,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $stmt->execute([
            ':completed' => $completed ? 1 : 0,
            ':completed_at' => $completed ? date('Y-m-d H:i:s') : null,
            ':id' => $existing['id']
        ]);
    } else {
        // Insert new record
        $stmt = $db->prepare("
            INSERT INTO exercise_completions 
            (user_id, split_day_id, exercise_id, completion_date, completed, completed_at, created_at, updated_at)
            VALUES (:user_id, :split_day_id, :exercise_id, :completion_date, :completed, :completed_at, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':split_day_id' => $splitDayId,
            ':exercise_id' => $exerciseId,
            ':completion_date' => $completionDate,
            ':completed' => $completed ? 1 : 0,
            ':completed_at' => $completed ? date('Y-m-d H:i:s') : null
        ]);
    }

    echo json_encode([
        'success' => true,
        'message' => $completed ? 'Exercise marked as completed' : 'Exercise marked as incomplete',
        'completed' => $completed
    ]);

} catch (Exception $e) {
    error_log('toggleExerciseCompletion.php Exception: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
