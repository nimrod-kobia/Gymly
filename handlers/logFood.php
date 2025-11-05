<?php
require_once "../autoload.php";

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    if (empty($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Not authenticated']);
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'error' => 'Invalid request method']);
        exit;
    }
    
    $userId = (int)$_SESSION['user_id'];
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Support both single food and array of foods
    $foods = isset($input['foods']) ? $input['foods'] : [$input];
    $mealType = $input['meal_type'] ?? 'snack';
    
    $db = (new Database())->connect();
    $db->beginTransaction();
    
    $loggedFoods = [];
    
    foreach ($foods as $foodItem) {
        $foodName = $foodItem['food_name'] ?? '';
        $servingSize = $foodItem['serving_size'] ?? '';
        $calories = $foodItem['calories'] ?? 0;
        $protein = $foodItem['protein_g'] ?? 0;
        $carbs = $foodItem['carbs_g'] ?? 0;
        $fat = $foodItem['fat_g'] ?? 0;
        
        if (empty($foodName)) {
            continue;
        }
        
        // Log meal
        $logStmt = $db->prepare("
            INSERT INTO user_meals (user_id, food_name, calories, protein_g, carbs_g, fat_g, serving_size, meal_type)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $logStmt->execute([
            $userId,
            $foodName,
            $calories,
            $protein,
            $carbs,
            $fat,
            $servingSize,
            $mealType
        ]);
        
        $loggedFoods[] = [
            'food_name' => $foodName,
            'calories' => $calories,
            'protein_g' => $protein,
            'carbs_g' => $carbs,
            'fat_g' => $fat,
            'serving_size' => $servingSize
        ];
    }
    
    // Update daily summary
    $today = date('Y-m-d');
    $totalCalories = array_sum(array_column($loggedFoods, 'calories'));
    $totalProtein = array_sum(array_column($loggedFoods, 'protein_g'));
    $totalCarbs = array_sum(array_column($loggedFoods, 'carbs_g'));
    $totalFat = array_sum(array_column($loggedFoods, 'fat_g'));
    
    $summaryStmt = $db->prepare("
        INSERT INTO user_daily_summary (user_id, summary_date, calories_consumed, protein_g, carbs_g, fat_g)
        VALUES (?, ?, ?, ?, ?, ?)
        ON CONFLICT (user_id, summary_date) DO UPDATE SET
            calories_consumed = user_daily_summary.calories_consumed + ?,
            protein_g = user_daily_summary.protein_g + ?,
            carbs_g = user_daily_summary.carbs_g + ?,
            fat_g = user_daily_summary.fat_g + ?,
            updated_at = NOW()
    ");
    $summaryStmt->execute([
        $userId, $today,
        $totalCalories, $totalProtein, $totalCarbs, $totalFat,
        $totalCalories, $totalProtein, $totalCarbs, $totalFat
    ]);
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'foods' => $loggedFoods,
        'message' => count($loggedFoods) . ' food(s) logged successfully'
    ]);
    
} catch (Exception $e) {
    if (isset($db)) $db->rollBack();
    error_log("logFood.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

