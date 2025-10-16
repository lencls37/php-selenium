<?php

require_once __DIR__ . '/vendor/autoload.php';

use Lencls37\PhpSelenium\SeleniumDriver;

echo "=== Testing PHP Selenium Driver ===\n\n";

try {
    echo "1. Creating SeleniumDriver instance...\n";
    $driver = new SeleniumDriver();
    
    echo "2. Initializing driver (detecting Chrome and downloading ChromeDriver)...\n";
    $driver->initialize();
    
    echo "\n3. Testing driver paths:\n";
    $driverPath = $driver->getDriverPath();
    $chromePath = $driver->getChromePath();
    
    echo "   ChromeDriver Path: $driverPath\n";
    echo "   Chrome Binary Path: $chromePath\n";
    
    echo "\n4. Verifying files exist:\n";
    if (file_exists($driverPath)) {
        echo "   ✓ ChromeDriver exists\n";
    } else {
        echo "   ✗ ChromeDriver NOT found\n";
    }
    
    if (file_exists($chromePath)) {
        echo "   ✓ Chrome binary exists\n";
    } else {
        echo "   ✗ Chrome binary NOT found\n";
    }
    
    echo "\n=== Test Completed Successfully ===\n";
    
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
