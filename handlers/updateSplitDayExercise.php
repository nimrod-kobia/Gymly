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

    $targetSets = isset($_POST['target_sets']) ? max(1, (int) $_POST['target_sets']) : null;
    $targetReps = isset($_POST['target_reps']) ? trim($_POST['target_reps']) : null;
    $targetRest = isset($_POST['target_rest_seconds']) ? max(0, (int) $_POST['target_rest_seconds']) : null;
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : null;

    $db = (new Database())->connect();
    if (!$db) {
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed'
        ]);
        exit;
    }

    // Ensure the entry belongs to the user's split
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

    $fields = [];
    $params = [
        ':entry_id' => $entryId
    ];

    if ($targetSets !== null) {
        $fields[] = 'target_sets = :target_sets';
        $params[':target_sets'] = $targetSets;
    }
    if ($targetReps !== null && $targetReps !== '') {
        $fields[] = 'target_reps = :target_reps';
        $params[':target_reps'] = substr($targetReps, 0, 50);
    }
    if ($targetRest !== null) {
        $fields[] = 'target_rest_seconds = :target_rest';
        $params[':target_rest'] = $targetRest;
    }
    if ($notes !== null) {
        $fields[] = 'notes = :notes';
        $params[':notes'] = $notes !== '' ? substr($notes, 0, 500) : null;
    }

    if (empty($fields)) {
        echo json_encode([
            'success' => true,
            'message' => 'No changes applied'
        ]);
        exit;
    }

    $fields[] = 'updated_at = NOW()';
    $query = 'UPDATE split_day_exercises SET ' . implode(', ', $fields) . ' WHERE id = :entry_id';

    $updateStmt = $db->prepare($query);
    $updateStmt->execute($params);

    $fetchStmt = $db->prepare("SELECT sde.*, e.name, e.muscle_group, e.equipment
        FROM split_day_exercises sde
        INNER JOIN exercises e ON sde.exercise_id = e.id
        WHERE sde.id = :id");
    $fetchStmt->execute([':id' => $entryId]);
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
        'message' => 'Exercise updated',
        'data' => $record
    ]);
} catch (PDOException $e) {
    error_log('updateSplitDayExercise.php PDOException: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);
}
