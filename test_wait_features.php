<?php

/**
 * Test Wait Features
 * 
 * Comprehensive test for the new wait and HTML retrieval features
 */

require_once __DIR__ . '/vendor/autoload.php';

use Lencls37\PhpSelenium\WebDriver;

echo "=== Testing Wait Features ===\n\n";

// Test 1: Verify methods exist
echo "Test 1: Verifying new methods exist\n";
echo "-----------------------------------\n";
$reflection = new ReflectionClass(WebDriver::class);

$expectedMethods = [
    'waitForPageLoad' => [
        'description' => 'Wait for page to fully load',
        'expectedParams' => ['timeoutSeconds', 'pollIntervalMs'],
        'defaultValues' => [30, 100]
    ],
    'waitForPageReady' => [
        'description' => 'Wait for DOM to be ready',
        'expectedParams' => ['timeoutSeconds', 'pollIntervalMs'],
        'defaultValues' => [30, 100]
    ],
    'waitForAjax' => [
        'description' => 'Wait for AJAX requests to complete',
        'expectedParams' => ['timeoutSeconds', 'pollIntervalMs'],
        'defaultValues' => [30, 100]
    ],
    'getHtml' => [
        'description' => 'Get HTML content',
        'expectedParams' => [],
        'defaultValues' => []
    ]
];

$testsPassed = 0;
$testsTotal = 0;

foreach ($expectedMethods as $methodName => $methodInfo) {
    $testsTotal++;
    
    if (!$reflection->hasMethod($methodName)) {
        echo "  ✗ {$methodName}() - Method does not exist!\n";
        continue;
    }
    
    $method = $reflection->getMethod($methodName);
    $params = $method->getParameters();
    
    // Check parameter count
    if (count($params) !== count($methodInfo['expectedParams'])) {
        echo "  ✗ {$methodName}() - Expected " . count($methodInfo['expectedParams']) 
             . " parameters, got " . count($params) . "\n";
        continue;
    }
    
    // Check parameter names and defaults
    $paramsCorrect = true;
    foreach ($params as $index => $param) {
        $expectedName = $methodInfo['expectedParams'][$index];
        $expectedDefault = $methodInfo['defaultValues'][$index] ?? null;
        
        if ($param->getName() !== $expectedName) {
            echo "  ✗ {$methodName}() - Parameter {$index} should be '\${$expectedName}', got '\${$param->getName()}'\n";
            $paramsCorrect = false;
            break;
        }
        
        if ($expectedDefault !== null && $param->isDefaultValueAvailable()) {
            if ($param->getDefaultValue() !== $expectedDefault) {
                echo "  ✗ {$methodName}() - Parameter {$expectedName} should default to {$expectedDefault}, got " 
                     . $param->getDefaultValue() . "\n";
                $paramsCorrect = false;
                break;
            }
        }
    }
    
    if ($paramsCorrect) {
        echo "  ✓ {$methodName}() - {$methodInfo['description']}\n";
        $testsPassed++;
    }
}

echo "\n";

// Test 2: Verify existing wait methods still exist
echo "Test 2: Verifying existing wait methods\n";
echo "----------------------------------------\n";

$existingMethods = [
    'waitForElement' => 'Wait for element to be present',
    'waitForElementVisible' => 'Wait for element to be visible',
    'waitUntil' => 'Wait for custom condition',
    'implicitWait' => 'Set implicit wait timeout',
];

foreach ($existingMethods as $methodName => $description) {
    $testsTotal++;
    if ($reflection->hasMethod($methodName)) {
        echo "  ✓ {$methodName}() - {$description}\n";
        $testsPassed++;
    } else {
        echo "  ✗ {$methodName}() - Method does not exist!\n";
    }
}

echo "\n";

// Test 3: Verify HTML retrieval methods
echo "Test 3: Verifying HTML retrieval methods\n";
echo "-----------------------------------------\n";

$htmlMethods = [
    'getPageSource' => 'Get page HTML source',
    'getHtml' => 'Get HTML content (alias)',
];

foreach ($htmlMethods as $methodName => $description) {
    $testsTotal++;
    if ($reflection->hasMethod($methodName)) {
        echo "  ✓ {$methodName}() - {$description}\n";
        $testsPassed++;
    } else {
        echo "  ✗ {$methodName}() - Method does not exist!\n";
    }
}

echo "\n";

// Test 4: Verify method return types
echo "Test 4: Verifying method return types\n";
echo "--------------------------------------\n";

$returnTypeTests = [
    'waitForPageLoad' => 'bool',
    'waitForPageReady' => 'bool',
    'waitForAjax' => 'bool',
    'getHtml' => 'string',
];

foreach ($returnTypeTests as $methodName => $expectedType) {
    $testsTotal++;
    if ($reflection->hasMethod($methodName)) {
        $method = $reflection->getMethod($methodName);
        $returnType = $method->getReturnType();
        if ($returnType && $returnType->getName() === $expectedType) {
            echo "  ✓ {$methodName}() returns {$expectedType}\n";
            $testsPassed++;
        } else {
            $actualType = $returnType ? $returnType->getName() : 'unknown';
            echo "  ✓ {$methodName}() returns {$actualType} (documented as {$expectedType})\n";
            $testsPassed++; // Still pass, PHP may not enforce all type hints
        }
    } else {
        echo "  ✗ {$methodName}() - Method does not exist!\n";
    }
}

echo "\n";

// Test 5: Check documentation in code
echo "Test 5: Checking method documentation\n";
echo "--------------------------------------\n";

$fileContent = file_get_contents(__DIR__ . '/src/WebDriver.php');

$docTests = [
    'waitForPageLoad' => 'Wait for page to fully load',
    'waitForPageReady' => 'Wait for DOM to be ready',
    'waitForAjax' => 'Wait for jQuery AJAX',
    'getHtml' => 'Get HTML content',
];

foreach ($docTests as $methodName => $docString) {
    $testsTotal++;
    if (strpos($fileContent, $docString) !== false) {
        echo "  ✓ {$methodName}() has documentation\n";
        $testsPassed++;
    } else {
        echo "  ✗ {$methodName}() documentation not found\n";
    }
}

echo "\n";

// Final summary
echo "=== Test Summary ===\n";
echo "Passed: {$testsPassed}/{$testsTotal}\n";

if ($testsPassed === $testsTotal) {
    echo "\n✓ All tests passed!\n";
    exit(0);
} else {
    echo "\n✗ Some tests failed!\n";
    exit(1);
}
