<?php
/**
 * Search Food - Instant search for food autocomplete with fallback
 */

require_once '../autoload.php';

header('Content-Type: application/json');

try {
    $query = trim($_GET['query'] ?? '');
    
    if (empty($query)) {
        echo json_encode(['success' => false, 'error' => 'Query required']);
        exit;
    }
    
    // Try Nutritionix API first
    $nutritionService = new NutritionService();
    $foods = $nutritionService->searchFoods($query);
    
    // If API fails or returns nothing, use local database
    if (empty($foods)) {
        $commonFoods = require_once __DIR__ . '/../database/common_foods.php';
        
        // Search local database
        $query = strtolower($query);
        $matches = array_filter($commonFoods, function($food) use ($query) {
            return stripos($food['food_name'], $query) !== false;
        });
        
        // Format for frontend
        $foods = array_map(function($food) {
            return [
                'food_name' => $food['food_name'],
                'serving_unit' => $food['serving'] ?? '1 serving',
                'tag_name' => $food['food_name']
            ];
        }, array_values($matches));
    }
    
    echo json_encode([
        'success' => true,
        'foods' => array_slice($foods, 0, 10) // Limit to 10 results
    ]);
    
} catch (Exception $e) {
    error_log("searchFood error: " . $e->getMessage());
    
    // On error, still try to return local results
    try {
        $commonFoods = require __DIR__ . '/../database/common_foods.php';
        $query = strtolower($_GET['query'] ?? '');
        
        $matches = array_filter($commonFoods, function($food) use ($query) {
            return stripos($food['food_name'], $query) !== false;
        });
        
        $foods = array_map(function($food) {
            return [
                'food_name' => $food['food_name'],
                'serving_unit' => $food['serving'] ?? '1 serving'
            ];
        }, array_values($matches));
        
        echo json_encode([
            'success' => true,
            'foods' => array_slice($foods, 0, 10)
        ]);
    } catch (Exception $fallbackError) {
        echo json_encode([
            'success' => false,
            'error' => 'Search failed',
            'details' => $e->getMessage()
        ]);
    }
}
