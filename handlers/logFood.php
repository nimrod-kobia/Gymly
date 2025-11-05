<?php
// Catch all errors and output as JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    require_once "../autoload.php";
    
    header('Content-Type: application/json');
    
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    if (empty($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Not authenticated']);
        exit;
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Initialization error: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

try {
    $userId = (int)$_SESSION['user_id'];
    $query = trim($_POST['query'] ?? '');
    $mealType = trim($_POST['meal_type'] ?? 'snack');
    
    if (empty($query)) {
        echo json_encode(['success' => false, 'error' => 'Food query is required']);
        exit;
    }
    
    $db = (new Database())->connect();
    $nutritionService = new NutritionService();
    
    // Check cache first
    $stmt = $db->prepare("SELECT * FROM food_cache WHERE query = ? AND cached_at > NOW() - INTERVAL '7 days' LIMIT 1");
    $stmt->execute([$query]);
    $cached = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($cached) {
        $foodData = [
            'food_name' => $cached['food_name'],
            'calories' => $cached['calories'],
            'protein_g' => $cached['protein_g'],
            'carbs_g' => $cached['carbs_g'],
            'fat_g' => $cached['fat_g'],
            'serving_size' => $cached['serving_size']
        ];
    } else {
        // Call API
        $foodData = $nutritionService->getNutritionInfo($query);
        
        // If API fails, try local database
        if (!$foodData) {
            $commonFoods = require __DIR__ . '/../database/common_foods.php';
            
            // Search for similar food
            $foodName = strtolower($query);
            foreach ($commonFoods as $food) {
                if (stripos($food['food_name'], $foodName) !== false || 
                    stripos($foodName, strtolower($food['food_name'])) !== false) {
                    $foodData = [
                        'food_name' => $food['food_name'],
                        'calories' => $food['calories'],
                        'protein_g' => $food['protein_g'],
                        'carbs_g' => $food['carbs_g'],
                        'fat_g' => $food['fat_g'],
                        'serving_size' => $food['serving']
                    ];
                    break;
                }
            }
            
            if (!$foodData) {
                echo json_encode(['success' => false, 'error' => 'Food not found. Try: chicken breast, rice, eggs, apple']);
                exit;
            }
        }
        
        // Cache result
        $cacheStmt = $db->prepare("
            INSERT INTO food_cache (query, food_name, calories, protein_g, carbs_g, fat_g, serving_size)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON CONFLICT (query) DO UPDATE SET cached_at = NOW()
        ");
        $cacheStmt->execute([
            $query,
            $foodData['food_name'],
            $foodData['calories'],
            $foodData['protein_g'],
            $foodData['carbs_g'],
            $foodData['fat_g'],
            $foodData['serving_size']
        ]);
    }
    // Log meal
    $logStmt = $db->prepare("
        INSERT INTO user_meals (user_id, food_name, calories, protein_g, carbs_g, fat_g, serving_size, meal_type)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $logStmt->execute([
        $userId,
        $foodData['food_name'],
        $foodData['calories'],
        $foodData['protein_g'],
        $foodData['carbs_g'],
        $foodData['fat_g'],
        $foodData['serving_size'],
        $mealType
    ]);
    
    // Update daily summary
    $today = date('Y-m-d');
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
        $foodData['calories'], $foodData['protein_g'], $foodData['carbs_g'], $foodData['fat_g'],
        $foodData['calories'], $foodData['protein_g'], $foodData['carbs_g'], $foodData['fat_g']
    ]);
    
    echo json_encode([
        'success' => true,
        'food' => $foodData,
        'message' => 'Food logged successfully'
    ]);
    
} catch (Exception $e) {
    error_log("logFood.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage(), 'trace' => $e->getTraceAsString()]);
}
?>
