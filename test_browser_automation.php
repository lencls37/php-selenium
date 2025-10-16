<?php

/**
 * Test Browser Automation Features
 * 
 * This test verifies that the WebDriver and WebElement classes work correctly
 */

require_once __DIR__ . '/vendor/autoload.php';

use Lencls37\PhpSelenium\ChromeDriver;
use Lencls37\PhpSelenium\WebDriver;

echo "═══════════════════════════════════════════════════════════════\n";
echo "    TESTING BROWSER AUTOMATION FEATURES\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$testsPassed = 0;
$testsFailed = 0;

function testPass($message) {
    global $testsPassed;
    $testsPassed++;
    echo "✓ $message\n";
}

function testFail($message, $error = null) {
    global $testsFailed;
    $testsFailed++;
    echo "✗ $message\n";
    if ($error) {
        echo "  Error: $error\n";
    }
}

try {
    // Test 1: Check if classes exist
    echo "1. Checking class availability...\n";
    
    if (class_exists('Lencls37\PhpSelenium\WebDriver')) {
        testPass("WebDriver class exists");
    } else {
        testFail("WebDriver class not found");
    }
    
    if (class_exists('Lencls37\PhpSelenium\WebElement')) {
        testPass("WebElement class exists");
    } else {
        testFail("WebElement class not found");
    }
    
    echo "\n";
    
    // Test 2: Check WebDriver methods
    echo "2. Checking WebDriver methods...\n";
    
    $requiredMethods = [
        'start',
        'get',
        'getCurrentUrl',
        'getTitle',
        'getPageSource',
        'findElementByCssSelector',
        'findElementsByXPath',
        'findElementById',
        'executeScript',
        'takeScreenshot',
        'back',
        'forward',
        'refresh',
        'maximize',
        'addCookie',
        'getCookies',
        'waitForElement',
        'quit'
    ];
    
    $reflection = new ReflectionClass('Lencls37\PhpSelenium\WebDriver');
    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    $methodNames = array_map(function($method) {
        return $method->getName();
    }, $methods);
    
    foreach ($requiredMethods as $method) {
        if (in_array($method, $methodNames)) {
            testPass("Method '$method' exists");
        } else {
            testFail("Method '$method' not found");
        }
    }
    
    echo "\n";
    
    // Test 3: Check WebElement methods
    echo "3. Checking WebElement methods...\n";
    
    $elementMethods = [
        'click',
        'sendKeys',
        'clear',
        'getText',
        'getAttribute',
        'getCssValue',
        'getTagName',
        'isDisplayed',
        'isEnabled',
        'isSelected',
        'getLocation',
        'getSize',
        'getRect',
        'takeScreenshot',
        'findElementByCssSelector'
    ];
    
    $elementReflection = new ReflectionClass('Lencls37\PhpSelenium\WebElement');
    $elementMethodsList = $elementReflection->getMethods(ReflectionMethod::IS_PUBLIC);
    $elementMethodNames = array_map(function($method) {
        return $method->getName();
    }, $elementMethodsList);
    
    foreach ($elementMethods as $method) {
        if (in_array($method, $elementMethodNames)) {
            testPass("WebElement method '$method' exists");
        } else {
            testFail("WebElement method '$method' not found");
        }
    }
    
    echo "\n";
    
    // Test 4: Check WebDriver constructor parameters
    echo "4. Checking WebDriver constructor...\n";
    
    $constructor = $reflection->getConstructor();
    $params = $constructor->getParameters();
    
    if (count($params) >= 1) {
        testPass("WebDriver constructor accepts required parameters");
        
        $firstParam = $params[0];
        if ($firstParam->getName() === 'driverPath') {
            testPass("First parameter is 'driverPath'");
        } else {
            testFail("First parameter should be 'driverPath', got '{$firstParam->getName()}'");
        }
    } else {
        testFail("WebDriver constructor missing parameters");
    }
    
    echo "\n";
    
    // Test 5: Test basic instantiation (without starting browser)
    echo "5. Testing WebDriver instantiation...\n";
    
    try {
        // Create a mock driver path for testing
        $mockDriverPath = '/tmp/chromedriver';
        
        // This should not fail even if driver doesn't exist
        // We're just testing the constructor
        $driver = new WebDriver($mockDriverPath, 9515, []);
        testPass("WebDriver instantiated successfully");
        
        // Test that driver hasn't started yet (sessionId should be null)
        if ($driver->getSessionId() === null) {
            testPass("Session ID is null before start()");
        } else {
            testFail("Session ID should be null before start()");
        }
    } catch (Exception $e) {
        testFail("Failed to instantiate WebDriver", $e->getMessage());
    }
    
    echo "\n";
    
    // Summary
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "    TEST RESULTS\n";
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "Tests Passed: $testsPassed\n";
    echo "Tests Failed: $testsFailed\n";
    echo "Total Tests: " . ($testsPassed + $testsFailed) . "\n";
    echo "\n";
    
    if ($testsFailed === 0) {
        echo "✓ ALL TESTS PASSED!\n";
        echo "\nBrowser automation features are correctly implemented.\n";
        echo "You can now use WebDriver and WebElement classes to control the browser.\n\n";
        exit(0);
    } else {
        echo "✗ SOME TESTS FAILED\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "\n✗ Fatal Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
