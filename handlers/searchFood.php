<?php
/**
 * Search Food - LOCAL DATABASE ONLY
 * Fast search through Kenyan foods database
 */

require_once '../autoload.php';
header('Content-Type: application/json');

try {
    $query = trim($_GET['query'] ?? '');
    
    if (empty($query)) {
        echo json_encode(['success' => false, 'message' => 'Query required']);
        exit;
    }
    
    // Load local foods database
    $commonFoods = require __DIR__ . '/../database/common_foods.php';
    
    // Search local database (case-insensitive)
    $queryLower = strtolower($query);
    $matches = array_filter($commonFoods, function($food) use ($queryLower) {
        return stripos($food['food_name'], $queryLower) !== false;
    });
    
    // Format results
    $foods = array_map(function($food) {
        return [
            'food_name' => $food['food_name'],
            'food_id' => 'local',
            'serving_unit' => count($food['servings']) . ' serving options available'
        ];
    }, array_values($matches));
    
    // Return results (limit to 10)
    echo json_encode([
        'success' => true,
        'foods' => array_slice($foods, 0, 10),
        'source' => 'local'
    ]);
    
} catch (Exception $e) {
    error_log("searchFood error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Search failed: ' . $e->getMessage()
    ]);
}
