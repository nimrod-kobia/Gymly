<?php

// Load Composer autoloader for PHPMailer 
$composerAutoloader = __DIR__ . '/vendor/autoload.php';
if (file_exists($composerAutoloader)) {
    require_once $composerAutoloader;
}

// Custom autoloader for classes and configuration files
// This autoloader will look for class files in the 'classes' and 'conf'
spl_autoload_register(function ($className) {
    $className = strtolower($className);
    
    $directories = [
        __DIR__ . "/classes/",
        __DIR__ . "/conf/",
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

// Load configuration automatically
if (!defined('DB_TYPE')) {
    require_once __DIR__ . '/conf/conf.php';
}

