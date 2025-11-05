<?php
// Test direct API call to Nutritionix
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$appId = 'a1516028';
$appKey = 'a85ab2f12f36968f9b637d300f308aa9';
$query = $_GET['query'] ?? '1 cup of rice';

$url = 'https://trackapi.nutritionix.com/v2/natural/nutrients';
$data = json_encode(['query' => $query]);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_HTTPHEADER => [
        'x-app-id: ' . $appId,
        'x-app-key: ' . $appKey,
        'Content-Type: application/json'
    ],
    // SSL options for development environment
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo json_encode([
        'success' => false,
        'error' => 'CURL error: ' . $error
    ]);
    exit;
}

if ($httpCode !== 200) {
    echo json_encode([
        'success' => false,
        'error' => 'API returned HTTP ' . $httpCode,
        'response' => $response
    ]);
    exit;
}

$result = json_decode($response, true);

if (empty($result['foods'])) {
    echo json_encode([
        'success' => false,
        'error' => 'No foods found',
        'raw_response' => $result
    ]);
    exit;
}

$food = $result['foods'][0];
echo json_encode([
    'success' => true,
    'food' => [
        'name' => $food['food_name'],
        'calories' => round($food['nf_calories']),
        'protein' => round($food['nf_protein'], 2),
        'carbs' => round($food['nf_total_carbohydrate'], 2),
        'fat' => round($food['nf_total_fat'], 2)
    ],
    'raw_response' => $result
]);
