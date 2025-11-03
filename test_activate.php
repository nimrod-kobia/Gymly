<?php
/**
 * Test activateSplit handler
 */

require_once "autoload.php";

// Simulate being logged in as user 78
$_SESSION['user_id'] = 78;
$_SESSION['last_activity'] = time();

echo "Testing activateSplit.php\n";
echo "=========================\n\n";

// Get a preset split ID
$db = (new Database())->connect();
$stmt = $db->prepare("SELECT id, split_name FROM workout_splits WHERE split_type = 'preset' AND user_id IS NULL LIMIT 1");
$stmt->execute();
$presetSplit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$presetSplit) {
    die("No preset split found\n");
}

echo "Found preset split: {$presetSplit['split_name']} (ID: {$presetSplit['id']})\n\n";

// Simulate POST request
$_POST['split_id'] = $presetSplit['id'];
$_SERVER['REQUEST_METHOD'] = 'POST';

echo "Attempting to activate split...\n";

ob_start();
include "handlers/activateSplit.php";
$output = ob_get_clean();

echo "Response:\n";
echo $output . "\n\n";

$result = json_decode($output, true);
if ($result && $result['success']) {
    echo "✓ Success!\n";
    
    // Check if split was created
    $stmt = $db->prepare("SELECT * FROM workout_splits WHERE user_id = 78 AND is_active = true");
    $stmt->execute();
    $activeSplit = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($activeSplit) {
        echo "Active split: {$activeSplit['split_name']}\n";
        
        // Check days
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM split_days WHERE split_id = :id");
        $stmt->execute([':id' => $activeSplit['id']]);
        $dayCount = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Days: {$dayCount['count']}\n";
    }
} else {
    echo "✗ Failed!\n";
    if ($result) {
        echo "Message: {$result['message']}\n";
    }
}
