<?php
/**
 * Get serving options for a specific food from LOCAL DATABASE
 */

header('Content-Type: application/json');
require_once '../autoload.php';

try {
    $foodId = $_GET['food_id'] ?? '';
    $foodName = $_GET['food_name'] ?? '';
    
    if (empty($foodName)) {
        echo json_encode(['success' => false, 'message' => 'Food name required']);
        exit;
    }
    
    // Load local foods database
    $commonFoods = require __DIR__ . '/../database/common_foods.php';
    
    // Find the food by name
    $food = null;
    foreach ($commonFoods as $item) {
        if (strcasecmp($item['food_name'], $foodName) === 0) {
            $food = $item;
            break;
        }
    }
    
    if (!$food || empty($food['servings'])) {
        echo json_encode(['success' => false, 'message' => 'No serving options found']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'food_name' => $foodName,
        'servings' => $food['servings']
    ]);
    
} catch (Exception $e) {
    error_log("getServingOptions error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
