<?php
/**
 * Log Health Metrics Handler
 * Processes health metric entries and stores them in the database
 */

require_once '../autoload.php';

header('Content-Type: application/json');

// Check authentication
if (!SessionManager::isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$userId = SessionManager::getUserId();

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

// Validate required fields
if (!isset($input['weight_kg']) || empty($input['weight_kg'])) {
    echo json_encode(['success' => false, 'error' => 'Weight is required']);
    exit;
}

try {
    $db = (new Database())->connect();
    
    // Prepare data
    $weight = floatval($input['weight_kg']);
    $height = !empty($input['height_cm']) ? floatval($input['height_cm']) : null;
    $bodyFat = !empty($input['body_fat_percentage']) ? floatval($input['body_fat_percentage']) : null;
    $muscleMass = !empty($input['muscle_mass_kg']) ? floatval($input['muscle_mass_kg']) : null;
    $bmi = !empty($input['bmi']) ? floatval($input['bmi']) : null;
    
    // Calculate BMI if not provided but height is available
    if (!$bmi && $height) {
        $heightM = $height / 100;
        $bmi = $weight / ($heightM * $heightM);
    }
    
    $heartRate = !empty($input['resting_heart_rate']) ? intval($input['resting_heart_rate']) : null;
    $bpSystolic = !empty($input['blood_pressure_systolic']) ? intval($input['blood_pressure_systolic']) : null;
    $bpDiastolic = !empty($input['blood_pressure_diastolic']) ? intval($input['blood_pressure_diastolic']) : null;
    $hoursSleep = !empty($input['hours_slept']) ? floatval($input['hours_slept']) : null;
    $waterIntake = !empty($input['water_intake_ml']) ? intval($input['water_intake_ml']) : null;
    $notes = !empty($input['notes']) ? trim($input['notes']) : null;
    
    // Validate ranges
    if ($weight < 20 || $weight > 500) {
        echo json_encode(['success' => false, 'error' => 'Invalid weight value']);
        exit;
    }
    
    if ($bodyFat !== null && ($bodyFat < 0 || $bodyFat > 100)) {
        echo json_encode(['success' => false, 'error' => 'Body fat percentage must be between 0 and 100']);
        exit;
    }
    
    if ($heartRate !== null && ($heartRate < 30 || $heartRate > 200)) {
        echo json_encode(['success' => false, 'error' => 'Heart rate must be between 30 and 200 bpm']);
        exit;
    }
    
    // Insert health metrics
    $stmt = $db->prepare("
        INSERT INTO user_health_metrics (
            user_id,
            weight_kg,
            height_cm,
            body_fat_percentage,
            muscle_mass_kg,
            bmi,
            resting_heart_rate,
            blood_pressure_systolic,
            blood_pressure_diastolic,
            hours_slept,
            water_intake_ml,
            notes
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        RETURNING id
    ");
    
    $stmt->execute([
        $userId,
        $weight,
        $height,
        $bodyFat,
        $muscleMass,
        $bmi,
        $heartRate,
        $bpSystolic,
        $bpDiastolic,
        $hoursSleep,
        $waterIntake,
        $notes
    ]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Health metrics logged successfully',
            'id' => $result['id'],
            'bmi' => $bmi ? round($bmi, 1) : null
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to log metrics']);
    }
    
} catch (PDOException $e) {
    error_log("Health metrics error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("Health metrics error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'An error occurred']);
}
