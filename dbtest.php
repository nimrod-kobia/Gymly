<?php
// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Database Connection Test</h2>";
echo "<pre>";

try {
    require_once __DIR__ . '/autoload.php';    
    require_once __DIR__ . '/conf/conf.php';

    echo " Autoloader Test \n";
    echo " Custom autoloader loaded successfully\n\n";

    // Test 1: Check if environment variables are loaded
    echo "Environment Variables Test \n";
    echo "DB_TYPE: " . (defined('DB_TYPE') ? DB_TYPE : 'NOT DEFINED') . "\n";
    echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED') . "\n";
    echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "\n";
    echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'NOT DEFINED') . "\n";
    echo "DB_PASS: " . (defined('DB_PASS') ? (strlen(DB_PASS) > 0 ? 'SET (hidden for security)' : 'EMPTY') : 'NOT DEFINED') . "\n";
    echo "DB_PORT: " . (defined('DB_PORT') ? DB_PORT : 'NOT DEFINED') . "\n";
    echo "DB_SSL_MODE: " . (defined('DB_SSL_MODE') ? DB_SSL_MODE : 'NOT DEFINED') . "\n";
    echo "DB_CHANNEL_BINDING: " . (defined('DB_CHANNEL_BINDING') ? DB_CHANNEL_BINDING : 'NOT DEFINED') . "\n\n";


    // Test 2: Check if Database class is autoloaded
    echo "=== Database Class Autoload Test ===\n";
    if (class_exists('Database')) {
        echo " Database class found via autoloader\n";
        
        $database = new Database();
        echo " Database instance created successfully\n\n";

        // Test 3: Try to connect to the database
        echo " Database Connection Test \n";

        //pdo variable to store returned instance from database class
        $pdo = $database->connect();
        
        if ($pdo) {
            echo " SUCCESS: Connected to database successfully!\n\n";
            
            // Test 4: Try a simple query
            echo "Simple Query Test \n";
            try {
                $stmt = $pdo->query("SELECT version() as db_version");
                $result = $stmt->fetch();
                echo "Database Version: " . $result['db_version'] . "\n\n";
                
                // Test 5: Check if we can access the gymly database
                echo " Database Access Test \n";
                $stmt = $pdo->query("SELECT current_database() as db_name, current_user as db_user");
                $result = $stmt->fetch();
                echo "Current Database: " . $result['db_name'] . "\n";
                echo "Current User: " . $result['db_user'] . "\n";
                
                echo "\nALL TESTS PASSED! Your database connection is working correctly.\n";
                
            } catch (PDOException $e) {
                echo "Query failed: " . $e->getMessage() . "\n";
                echo "This might indicate permission issues or that the database doesn't exist.\n";
            }
            
        } else {
            echo "FAILED: Could not connect to database.\n";
            echo "Check your credentials and ensure the database server is accessible.\n";
        }
    } else {
        echo " Database class not found. Check your autoloader configuration.\n";
        echo "Make sure:\n";
        echo "1. Database class is in classes/database.php\n";
        echo "2. The filename is lowercase: database.php\n";
        echo "3. The class name is Database (with capital D)\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Check your configuration and ensure all required files are included.\n";
}

echo "</pre>";
echo "<p>Test completed. If you see any errors, check your .env file and database credentials.</p>";
?>
