<?php

// Load Composer autoloader for PHPMailer 
$composerAutoloader = __DIR__ . '/vendor/autoload.php';
if (file_exists($composerAutoloader)) {
    require_once $composerAutoloader;
}

// Custom autoloader for project files (classes, config, services).
// This also supports basic PSR-0/namespace -> directory mapping.
spl_autoload_register(function ($className) {
    $className = ltrim($className, "\\");

    // For Services namespace, map to lowercase services directory
    if (strpos($className, 'Services\\') === 0) {
        $relativePath = 'services/' . substr($className, 9) . '.php';
        $filePath = __DIR__ . '/' . $relativePath;
        if (file_exists($filePath)) {
            require_once $filePath;
            return;
        }
    }

    // Try both original case and lowercase for classes
    $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
    $relativePathLower = strtolower($relativePath);

    $directories = [
        __DIR__ . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR,
        __DIR__ . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR,
        __DIR__ . DIRECTORY_SEPARATOR,
    ];

    foreach ($directories as $directory) {
        // Try original case first
        $filePath = $directory . $relativePath;
        if (file_exists($filePath)) {
            require_once $filePath;
            return;
        }
        
        // Try lowercase
        $filePathLower = $directory . $relativePathLower;
        if (file_exists($filePathLower)) {
            require_once $filePathLower;
            return;
        }
    }

    error_log("Autoload failed for: " . $className);
});

// Load configuration automatically
if (!defined('DB_TYPE')) {
    require_once __DIR__ . '/conf/conf.php';
}

