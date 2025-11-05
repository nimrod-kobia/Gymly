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

    $overview = [
        'active_split' => null,
        'splits' => [],
        'sessions' => []
    ];

    // Fetch user's splits (custom + copies of presets)
    $splitsStmt = $db->prepare("SELECT id, split_name, split_type, description, is_active, created_at, updated_at
        FROM workout_splits
        WHERE user_id = :user_id
        ORDER BY is_active DESC, updated_at DESC");
    $splitsStmt->execute([':user_id' => $userId]);
    $splits = $splitsStmt->fetchAll(PDO::FETCH_ASSOC);

    $splitIds = array_map(fn($split) => (int)$split['id'], $splits);

    $dayCounts = [];
    $exerciseCounts = [];

    if (count($splitIds) > 0) {
        $placeholders = implode(',', array_fill(0, count($splitIds), '?'));

        $dayStmt = $db->prepare("SELECT split_id, COUNT(*) AS day_count
            FROM split_days
            WHERE split_id IN ($placeholders)
            GROUP BY split_id");
        $dayStmt->execute($splitIds);
        foreach ($dayStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $dayCounts[(int)$row['split_id']] = (int)$row['day_count'];
        }

        $exerciseStmt = $db->prepare("SELECT sd.split_id, COUNT(*) AS exercise_count
            FROM split_day_exercises sde
            INNER JOIN split_days sd ON sde.split_day_id = sd.id
            WHERE sd.split_id IN ($placeholders)
            GROUP BY sd.split_id");
        $exerciseStmt->execute($splitIds);
        foreach ($exerciseStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $exerciseCounts[(int)$row['split_id']] = (int)$row['exercise_count'];
        }
    }

    $activeSplitId = null;
    foreach ($splits as &$split) {
        $splitId = (int)$split['id'];
        $split['id'] = $splitId;
        $split['is_active'] = filter_var($split['is_active'], FILTER_VALIDATE_BOOLEAN);
        $split['day_count'] = $dayCounts[$splitId] ?? 0;
        $split['exercise_count'] = $exerciseCounts[$splitId] ?? 0;
        $split['created_at'] = $split['created_at'] ?? null;
        $split['updated_at'] = $split['updated_at'] ?? null;

        if ($split['is_active']) {
            $activeSplitId = $splitId;
        }
    }
    unset($split);

    $overview['splits'] = $splits;

    if ($activeSplitId !== null) {
        $activeSplitStmt = $db->prepare("SELECT * FROM workout_splits WHERE id = :id AND user_id = :user_id LIMIT 1");
        $activeSplitStmt->execute([
            ':id' => $activeSplitId,
            ':user_id' => $userId
        ]);
        $activeSplit = $activeSplitStmt->fetch(PDO::FETCH_ASSOC);

        if ($activeSplit) {
            $activeSplit['id'] = (int)$activeSplit['id'];
            $activeSplit['is_active'] = true;

            $daysStmt = $db->prepare("SELECT * FROM split_days WHERE split_id = :split_id ORDER BY display_order ASC");
            $daysStmt->execute([':split_id' => $activeSplitId]);
            $days = $daysStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($days as &$day) {
                $day['id'] = (int)$day['id'];
                $day['split_id'] = (int)$day['split_id'];
                $day['day_of_week'] = $day['day_of_week'] !== null ? (int)$day['day_of_week'] : null;
                $day['display_order'] = $day['display_order'] !== null ? (int)$day['display_order'] : null;
                $day['is_rest_day'] = filter_var($day['is_rest_day'], FILTER_VALIDATE_BOOLEAN);

                $exerciseStmt = $db->prepare("SELECT sde.*, e.name, e.muscle_group, e.equipment
                    FROM split_day_exercises sde
                    INNER JOIN exercises e ON sde.exercise_id = e.id
                    WHERE sde.split_day_id = :day_id
                    ORDER BY sde.display_order ASC");
                $exerciseStmt->execute([':day_id' => $day['id']]);
                $exercises = $exerciseStmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($exercises as &$exercise) {
                    $exercise['id'] = (int)$exercise['id'];
                    $exercise['split_day_id'] = (int)$exercise['split_day_id'];
                    $exercise['exercise_id'] = (int)$exercise['exercise_id'];
                    $exercise['target_sets'] = $exercise['target_sets'] !== null ? (int)$exercise['target_sets'] : null;
                    $exercise['target_reps'] = $exercise['target_reps'] !== null ? (int)$exercise['target_reps'] : null;
                    $exercise['target_rest_seconds'] = $exercise['target_rest_seconds'] !== null ? (int)$exercise['target_rest_seconds'] : null;
                }
                unset($exercise);

                $day['exercises'] = $exercises;
            }
            unset($day);

            $activeSplit['days'] = $days;
            $overview['active_split'] = $activeSplit;
        }
    }

    echo json_encode([
        'success' => true,
        'data' => $overview
    ]);
} catch (PDOException $e) {
    error_log('fetchMyWorkoutsOverview.php PDOException: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);
} catch (Throwable $e) {
    error_log('fetchMyWorkoutsOverview.php Exception: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Unexpected error'
    ]);
}
?>
