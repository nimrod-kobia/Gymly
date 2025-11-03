<?php
/**
 * Test the workout handlers
 */

require_once "autoload.php";

// Simulate being logged in as user 78
$_SESSION['user_id'] = 78;
$_SESSION['last_activity'] = time();

echo "Testing Workout Handlers\n";
echo "========================\n\n";

// Test 1: Get Active Split
echo "1. Testing getActiveSplit.php\n";
$_GET = [];
ob_start();
include "handlers/getActiveSplit.php";
$output = ob_get_clean();
$result = json_decode($output, true);
echo "   Result: " . ($result['success'] ? "✓ Success" : "✗ Failed") . "\n";
if ($result['success']) {
    echo "   Split: " . $result['data']['split_name'] . "\n";
    $splitId = $result['data']['id'];
} else {
    echo "   Message: " . $result['message'] . "\n";
    exit;
}
echo "\n";

// Test 2: Get Split Details
echo "2. Testing getSplitDetails.php\n";
$_GET = ['split_id' => $splitId];
ob_start();
include "handlers/getSplitDetails.php";
$output = ob_get_clean();
$result = json_decode($output, true);
echo "   Result: " . ($result['success'] ? "✓ Success" : "✗ Failed") . "\n";
if ($result['success']) {
    echo "   Split: " . $result['data']['split']['split_name'] . "\n";
    echo "   Days: " . count($result['data']['days']) . "\n";
    if (count($result['data']['days']) > 0) {
        $firstDay = $result['data']['days'][0];
        echo "   First Day: " . $firstDay['day_name'] . " with " . count($firstDay['exercises']) . " exercises\n";
        if (count($firstDay['exercises']) > 0) {
            echo "   First Exercise: " . $firstDay['exercises'][0]['name'] . "\n";
        }
    }
} else {
    echo "   Message: " . $result['message'] . "\n";
}
echo "\n";

echo "========================\n";
echo "All tests completed!\n";
