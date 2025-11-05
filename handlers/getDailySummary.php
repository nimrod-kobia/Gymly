<?php
/**
 * Get Daily Nutrition Summary
 * Returns today's calorie and macro totals with meal history
 */

require_once '../autoload.php';

header('Content-Type: application/json');

try {
    // Check authentication
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Not authenticated']);
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    $today = date('Y-m-d');
    
    $db = (new Database())->connect();
    
    // Get daily summary
    $stmt = $db->prepare("
        SELECT 
            calories_consumed,
            protein_g,
            carbs_g,
            fat_g,
            meals_count
        FROM user_daily_summary
        WHERE user_id = :userId AND summary_date = :date
    ");
    
    $stmt->execute([
        'userId' => $userId,
        'date' => $today
    ]);
    
    $summary = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get today's meals
    $stmt = $db->prepare("
        SELECT 
            food_name,
            calories,
            protein_g,
            carbs_g,
            fat_g,
            serving_size,
            meal_type,
            TO_CHAR(logged_at, 'HH24:MI') as time
        FROM user_meals
        WHERE user_id = :userId 
        AND DATE(logged_at) = :date
        ORDER BY logged_at DESC
    ");
    
    $stmt->execute([
        'userId' => $userId,
        'date' => $today
    ]);
    
    $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'summary' => [
            'calories_consumed' => (int)($summary['calories_consumed'] ?? 0),
            'protein_g' => round($summary['protein_g'] ?? 0, 1),
            'carbs_g' => round($summary['carbs_g'] ?? 0, 1),
            'fat_g' => round($summary['fat_g'] ?? 0, 1),
            'meals_count' => (int)($summary['meals_count'] ?? 0)
        ],
        'meals' => $meals,
        'date' => $today
    ]);
    
} catch (Exception $e) {
    error_log("getDailySummary error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch daily summary',
        'details' => $e->getMessage()
    ]);
}
