<?php
// Basic Session Test - Minimal version
echo "<h1>Basic Session Test</h1>";

// Start session
session_start();

// Check if SessionManager exists
$sessionManagerPath = __DIR__ . '/../classes/SessionManager.php';
if (file_exists($sessionManagerPath)) {
    require_once $sessionManagerPath;
    echo "<p style='color: green;'>✅ SessionManager.php loaded</p>";
} else {
    echo "<p style='color: red;'>❌ SessionManager.php NOT found at: $sessionManagerPath</p>";
}

// Show current session
echo "<h3>Current Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Simple test
echo "<h3>Quick Test:</h3>";
echo "<a href='?test=1'>Set test data</a> | ";
echo "<a href='?test=2'>Clear session</a>";

if (isset($_GET['test'])) {
    if ($_GET['test'] == '1') {
        $_SESSION['test_user'] = 'john_doe';
        $_SESSION['test_time'] = time();
        header("Location: basic_test.php");
        exit;
    } elseif ($_GET['test'] == '2') {
        session_destroy();
        header("Location: basic_test.php");
        exit;
    }
}
?>