<?php
// Your custom autoloader for project classes
spl_autoload_register(function ($className) {
    $className = strtolower($className);
    
    $directories = [
        __DIR__ . "/classes/",
    ];
    
    foreach ($directories as $directory) {
        $filePath = $directory . $className . ".php";
        
        if (file_exists($filePath)) {
            require_once $filePath;
            return;
        }
    }
    
    error_log("Autoload failed for: " . $className);
});

// Load Composer autoloader for PHPMailer (if it exists)
$composerAutoloader = __DIR__ . '/vendor/autoload.php';
if (file_exists($composerAutoloader)) {
    require_once $composerAutoloader;
}
?>