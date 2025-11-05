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

    $userId = SessionManager::getUserId();
    $entryId = isset($_POST['split_day_exercise_id']) ? (int) $_POST['split_day_exercise_id'] : 0;

    if ($entryId <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Exercise entry required'
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

    // Verify ownership
    $ownershipStmt = $db->prepare("SELECT sde.id FROM split_day_exercises sde
        INNER JOIN split_days sd ON sde.split_day_id = sd.id
        INNER JOIN workout_splits ws ON sd.split_id = ws.id
        WHERE sde.id = :entry_id AND ws.user_id = :user_id
        LIMIT 1");
    $ownershipStmt->execute([
        ':entry_id' => $entryId,
        ':user_id' => $userId
    ]);

    if (!$ownershipStmt->fetchColumn()) {
        echo json_encode([
            'success' => false,
            'message' => 'Exercise entry not found'
        ]);
        exit;
    }

    $deleteStmt = $db->prepare('DELETE FROM split_day_exercises WHERE id = :entry_id');
    $deleteStmt->execute([':entry_id' => $entryId]);

    echo json_encode([
        'success' => true,
        'message' => 'Exercise removed from split day'
    ]);
} catch (PDOException $e) {
    error_log('deleteSplitDayExercise.php PDOException: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);
}
