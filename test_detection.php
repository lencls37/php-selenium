<?php

require_once __DIR__ . '/vendor/autoload.php';

use Lencls37\PhpSelenium\SeleniumDriver;
use Lencls37\PhpSelenium\FirefoxDriver;
use Lencls37\PhpSelenium\EdgeDriver;

echo "=== PHP Selenium Driver - Browser Detection Test ===\n\n";

// Test 1: Chrome Detection
echo "1. Testing Chrome/Chromium Detection:\n";
echo str_repeat("-", 50) . "\n";
try {
    $chromeDriver = new SeleniumDriver();
    
    // Use reflection to access private methods for testing
    $reflection = new ReflectionClass($chromeDriver);
    
    // Test OS detection
    $osMethod = $reflection->getMethod('detectOS');
    $osMethod->setAccessible(true);
    $os = $osMethod->invoke($chromeDriver);
    echo "   Detected OS: $os\n";
    
    // Test Chrome path detection
    $chromePath = $chromeDriver->getChromePath();
    if ($chromePath) {
        echo "   ✓ Chrome found at: $chromePath\n";
        
        // Test version detection
        $versionMethod = $reflection->getMethod('detectChromeVersion');
        $versionMethod->setAccessible(true);
        $version = $versionMethod->invoke($chromeDriver);
        
        if ($version) {
            echo "   ✓ Chrome version: $version\n";
            $majorVersion = explode('.', $version)[0];
            echo "   ✓ Major version: $majorVersion\n";
        }
    } else {
        echo "   ✗ Chrome not found on system\n";
        echo "   Note: In this case, the library would prompt to download Chrome\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Firefox Detection
echo "2. Testing Firefox Detection:\n";
echo str_repeat("-", 50) . "\n";
try {
    $firefoxDriver = new FirefoxDriver();
    
    $firefoxPath = $firefoxDriver->getBrowserPath();
    if ($firefoxPath) {
        echo "   ✓ Firefox found at: $firefoxPath\n";
        
        $reflection = new ReflectionClass($firefoxDriver);
        $versionMethod = $reflection->getMethod('detectBrowserVersion');
        $versionMethod->setAccessible(true);
        $version = $versionMethod->invoke($firefoxDriver);
        
        if ($version) {
            echo "   ✓ Firefox version: $version\n";
        }
    } else {
        echo "   ✗ Firefox not found on system\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Edge Detection
echo "3. Testing Edge Detection:\n";
echo str_repeat("-", 50) . "\n";
try {
    $edgeDriver = new EdgeDriver();
    
    $edgePath = $edgeDriver->getBrowserPath();
    if ($edgePath) {
        echo "   ✓ Edge found at: $edgePath\n";
        
        $reflection = new ReflectionClass($edgeDriver);
        $versionMethod = $reflection->getMethod('detectBrowserVersion');
        $versionMethod->setAccessible(true);
        $version = $versionMethod->invoke($edgeDriver);
        
        if ($version) {
            echo "   ✓ Edge version: $version\n";
        }
    } else {
        echo "   ✗ Edge not found on system\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Class Structure
echo "4. Testing Class Structure:\n";
echo str_repeat("-", 50) . "\n";

$classes = [
    'Lencls37\PhpSelenium\SeleniumDriver',
    'Lencls37\PhpSelenium\BrowserDriver',
    'Lencls37\PhpSelenium\FirefoxDriver',
    'Lencls37\PhpSelenium\EdgeDriver',
];

foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "   ✓ $class exists\n";
    } else {
        echo "   ✗ $class NOT found\n";
    }
}

echo "\n";

// Test 5: Required Methods
echo "5. Testing Required Methods:\n";
echo str_repeat("-", 50) . "\n";

$requiredMethods = ['initialize', 'getDriverPath', 'getChromePath'];
$chromeDriver = new SeleniumDriver();

foreach ($requiredMethods as $method) {
    if (method_exists($chromeDriver, $method)) {
        echo "   ✓ Method $method() exists\n";
    } else {
        echo "   ✗ Method $method() NOT found\n";
    }
}

echo "\n=== All Detection Tests Completed ===\n";
echo "\nNote: This test validates browser detection and class structure.\n";
echo "Driver downloads require internet access which may be limited in this environment.\n";
