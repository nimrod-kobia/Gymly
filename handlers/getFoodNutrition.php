<?php
/**
 * Get Food Nutrition - LOCAL DATABASE ONLY
 */

require_once '../autoload.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $foodName = $input['food_name'] ?? '';
    $servingSize = $input['serving_size'] ?? '';
    
    if (empty($foodName)) {
        echo json_encode(['success' => false, 'message' => 'Food name required']);
        exit;
    }
    
    // Load local foods database
    $commonFoods = require __DIR__ . '/../database/common_foods.php';
    
    // Find the food
    $food = null;
    foreach ($commonFoods as $item) {
        if (strcasecmp($item['food_name'], $foodName) === 0) {
            $food = $item;
            break;
        }
    }
    
    if (!$food) {
        echo json_encode(['success' => false, 'message' => 'Food not found']);
        exit;
    }
    
    // Find matching serving or use first one
    $serving = $food['servings'][0]; // Default to first serving
    
    if (!empty($servingSize)) {
        foreach ($food['servings'] as $s) {
            if (stripos($s['description'], $servingSize) !== false) {
                $serving = $s;
                break;
            }
        }
    }
    
    // Return nutrition data
    echo json_encode([
        'success' => true,
        'nutrition' => [
            'food_name' => $foodName,
            'serving_size' => $serving['description'],
            'calories' => $serving['calories'],
            'protein_g' => $serving['protein'],
            'carbs_g' => $serving['carbs'],
            'fat_g' => $serving['fat']
        ]
    ]);
    
} catch (Exception $e) {
    error_log("getFoodNutrition error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
