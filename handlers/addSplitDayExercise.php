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
    $splitDayId = isset($_POST['split_day_id']) ? (int) $_POST['split_day_id'] : 0;
    $exerciseId = isset($_POST['exercise_id']) ? (int) $_POST['exercise_id'] : 0;
    $targetSets = isset($_POST['target_sets']) ? max(1, (int) $_POST['target_sets']) : 3;
    $targetReps = trim($_POST['target_reps'] ?? '8-12');
    $targetRest = isset($_POST['target_rest_seconds']) ? max(0, (int) $_POST['target_rest_seconds']) : 90;
    $notes = trim($_POST['notes'] ?? '');

    if ($splitDayId <= 0 || $exerciseId <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Split day and exercise are required'
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

    // Ensure the split day belongs to the authenticated user
    $ownershipStmt = $db->prepare("SELECT sd.id FROM split_days sd
        INNER JOIN workout_splits ws ON sd.split_id = ws.id
        WHERE sd.id = :day_id AND ws.user_id = :user_id
        LIMIT 1");
    $ownershipStmt->execute([
        ':day_id' => $splitDayId,
        ':user_id' => $userId
    ]);

    if (!$ownershipStmt->fetchColumn()) {
        echo json_encode([
            'success' => false,
            'message' => 'Split day not found'
        ]);
        exit;
    }

    // Determine next display order
    $orderStmt = $db->prepare("SELECT COALESCE(MAX(display_order), 0) + 1 FROM split_day_exercises WHERE split_day_id = :day_id");
    $orderStmt->execute([':day_id' => $splitDayId]);
    $nextOrder = (int) $orderStmt->fetchColumn();
    if ($nextOrder <= 0) {
        $nextOrder = 1;
    }

    $insertStmt = $db->prepare("INSERT INTO split_day_exercises
        (split_day_id, exercise_id, target_sets, target_reps, target_rest_seconds, notes, display_order, created_at, updated_at)
        VALUES (:split_day_id, :exercise_id, :target_sets, :target_reps, :target_rest, :notes, :display_order, NOW(), NOW())");
    $insertStmt->execute([
        ':split_day_id' => $splitDayId,
        ':exercise_id' => $exerciseId,
        ':target_sets' => $targetSets,
        ':target_reps' => $targetReps !== '' ? substr($targetReps, 0, 50) : '8-12',
        ':target_rest' => $targetRest,
        ':notes' => $notes !== '' ? $notes : null,
        ':display_order' => $nextOrder
    ]);

    $newId = (int) $db->lastInsertId('split_day_exercises_id_seq');

    $fetchStmt = $db->prepare("SELECT sde.*, e.name, e.muscle_group, e.equipment
        FROM split_day_exercises sde
        INNER JOIN exercises e ON sde.exercise_id = e.id
        WHERE sde.id = :id");
    $fetchStmt->execute([':id' => $newId]);
    $record = $fetchStmt->fetch(PDO::FETCH_ASSOC);

    if ($record) {
        $record['id'] = (int) $record['id'];
        $record['split_day_id'] = (int) $record['split_day_id'];
        $record['exercise_id'] = (int) $record['exercise_id'];
        $record['target_sets'] = $record['target_sets'] !== null ? (int) $record['target_sets'] : null;
        $record['target_rest_seconds'] = $record['target_rest_seconds'] !== null ? (int) $record['target_rest_seconds'] : null;
        $record['display_order'] = $record['display_order'] !== null ? (int) $record['display_order'] : null;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Exercise added to split day',
        'data' => $record
    ]);
} catch (PDOException $e) {
    error_log('addSplitDayExercise.php PDOException: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);
}
