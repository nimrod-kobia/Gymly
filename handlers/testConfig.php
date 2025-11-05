<?php
// Direct API test without authentication requirement
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../autoload.php";

header('Content-Type: application/json');

try {
    echo json_encode([
        'success' => true,
        'message' => 'Autoload works',
        'defines' => [
            'NUTRITIONIX_APP_ID' => defined('NUTRITIONIX_APP_ID') ? 'defined' : 'not defined',
            'NUTRITIONIX_APP_KEY' => defined('NUTRITIONIX_APP_KEY') ? 'defined' : 'not defined',
            'DB_TYPE' => defined('DB_TYPE') ? DB_TYPE : 'not defined'
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
