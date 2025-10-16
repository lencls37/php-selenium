<?php

/**
 * Stealth Mode Example
 * 
 * This example demonstrates how to use stealth mode to bypass bot detection
 * and anti-automation protection systems.
 * 
 * Stealth mode is ENABLED BY DEFAULT to help bypass common bot protections.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Lencls37\PhpSelenium\ChromeDriver;
use Lencls37\PhpSelenium\WebDriver;
use Lencls37\PhpSelenium\StealthConfig;

echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║              STEALTH MODE DEMONSTRATION                       ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

// Setup ChromeDriver
echo "1. Setting up ChromeDriver...\n";
$seleniumDriver = new ChromeDriver();
$seleniumDriver->initialize();
echo "   ✓ ChromeDriver ready\n\n";

// Example 1: Default Stealth Mode (ENABLED by default)
echo "═══════════════════════════════════════════════════════════════\n";
echo "EXAMPLE 1: Default Stealth Mode (Enabled)\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$driver1 = new WebDriver($seleniumDriver->getDriverPath(), 9515, [
    'goog:chromeOptions' => [
        'binary' => $seleniumDriver->getChromePath(),
        'args' => ['--headless', '--disable-gpu', '--no-sandbox']
    ]
]);
// No need to pass StealthConfig - it's enabled by default!

$driver1->start();
echo "✓ Browser started with DEFAULT stealth mode (enabled)\n";

// Test stealth mode
$driver1->get('data:text/html,<html><body><h1>Testing Stealth Mode</h1></body></html>');

// Check if webdriver property is hidden
$isWebdriverHidden = $driver1->executeScript('return navigator.webdriver === undefined;');
echo "✓ navigator.webdriver hidden: " . ($isWebdriverHidden ? 'YES ✓' : 'NO ✗') . "\n";

// Check Chrome object
$hasChromeObject = $driver1->executeScript('return typeof window.chrome !== "undefined";');
echo "✓ window.chrome exists: " . ($hasChromeObject ? 'YES ✓' : 'NO ✗') . "\n";

// Check plugins
$pluginsCount = $driver1->executeScript('return navigator.plugins.length;');
echo "✓ Plugins count: $pluginsCount (looks natural ✓)\n";

$driver1->quit();
echo "\n";

// Example 2: Custom Stealth Configuration
echo "═══════════════════════════════════════════════════════════════\n";
echo "EXAMPLE 2: Custom Stealth Configuration\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$customStealth = StealthConfig::custom([
    'enabled' => true,
    'hideWebdriver' => true,
    'hideAutomation' => true,
    'userAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'disableInfobars' => true,
    'excludeSwitches' => true,
    'modifyPermissions' => true
]);

$driver2 = new WebDriver($seleniumDriver->getDriverPath(), 9516, [
    'goog:chromeOptions' => [
        'binary' => $seleniumDriver->getChromePath(),
        'args' => ['--headless', '--disable-gpu', '--no-sandbox']
    ]
], $customStealth);

$driver2->start();
echo "✓ Browser started with CUSTOM stealth configuration\n";

$driver2->get('data:text/html,<html><body><h1>Custom Stealth</h1></body></html>');

// Check user agent
$userAgent = $driver2->executeScript('return navigator.userAgent;');
echo "✓ User Agent: " . substr($userAgent, 0, 50) . "...\n";

$driver2->quit();
echo "\n";

// Example 3: Disabled Stealth Mode
echo "═══════════════════════════════════════════════════════════════\n";
echo "EXAMPLE 3: Disabled Stealth Mode (for testing)\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$disabledStealth = StealthConfig::disabled();

$driver3 = new WebDriver($seleniumDriver->getDriverPath(), 9517, [
    'goog:chromeOptions' => [
        'binary' => $seleniumDriver->getChromePath(),
        'args' => ['--headless', '--disable-gpu', '--no-sandbox']
    ]
], $disabledStealth);

$driver3->start();
echo "✓ Browser started with stealth mode DISABLED\n";

$driver3->get('data:text/html,<html><body><h1>No Stealth</h1></body></html>');

// Check if webdriver is exposed (should be true when disabled)
$isWebdriverExposed = $driver3->executeScript('return navigator.webdriver === true;');
echo "✓ navigator.webdriver exposed: " . ($isWebdriverExposed ? 'YES (stealth disabled)' : 'NO') . "\n";

$driver3->quit();
echo "\n";

// Example 4: Testing Bot Detection
echo "═══════════════════════════════════════════════════════════════\n";
echo "EXAMPLE 4: Bot Detection Test\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Create HTML with bot detection script
$botDetectionHtml = <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <title>Bot Detection Test</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .result { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .pass { background: #d4edda; color: #155724; }
        .fail { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>Bot Detection Results</h1>
    <div id="results"></div>
    <script>
        let results = [];
        
        // Test 1: Check navigator.webdriver
        if (navigator.webdriver === undefined) {
            results.push({ test: 'navigator.webdriver', status: 'PASS', detail: 'Hidden (not detected)' });
        } else {
            results.push({ test: 'navigator.webdriver', status: 'FAIL', detail: 'Exposed: ' + navigator.webdriver });
        }
        
        // Test 2: Check window.chrome
        if (typeof window.chrome !== 'undefined') {
            results.push({ test: 'window.chrome', status: 'PASS', detail: 'Present' });
        } else {
            results.push({ test: 'window.chrome', status: 'FAIL', detail: 'Missing' });
        }
        
        // Test 3: Check plugins
        if (navigator.plugins.length > 0) {
            results.push({ test: 'Plugins', status: 'PASS', detail: navigator.plugins.length + ' plugins' });
        } else {
            results.push({ test: 'Plugins', status: 'FAIL', detail: 'No plugins (suspicious)' });
        }
        
        // Test 4: Check languages
        if (navigator.languages && navigator.languages.length > 0) {
            results.push({ test: 'Languages', status: 'PASS', detail: navigator.languages.join(', ') });
        } else {
            results.push({ test: 'Languages', status: 'FAIL', detail: 'No languages' });
        }
        
        // Display results
        let html = '';
        results.forEach(r => {
            const className = r.status === 'PASS' ? 'pass' : 'fail';
            html += `<div class="result ${className}">
                <strong>${r.test}:</strong> ${r.status} - ${r.detail}
            </div>`;
        });
        document.getElementById('results').innerHTML = html;
        
        // Store for retrieval
        window.botDetectionResults = results;
    </script>
</body>
</html>
HTML;

$htmlFile = '/tmp/bot_detection_test.html';
file_put_contents($htmlFile, $botDetectionHtml);

$driver4 = new WebDriver($seleniumDriver->getDriverPath(), 9518, [
    'goog:chromeOptions' => [
        'binary' => $seleniumDriver->getChromePath(),
        'args' => ['--headless', '--disable-gpu', '--no-sandbox']
    ]
]);
// Using default stealth mode

$driver4->start();
$driver4->get('file://' . $htmlFile);

// Wait for tests to complete
sleep(1);

// Get results
$results = $driver4->executeScript('return window.botDetectionResults;');

echo "Bot Detection Test Results:\n";
echo "----------------------------\n";
foreach ($results as $result) {
    $icon = $result['status'] === 'PASS' ? '✓' : '✗';
    echo "$icon {$result['test']}: {$result['status']} - {$result['detail']}\n";
}

// Take screenshot
$driver4->saveScreenshot('/tmp/bot_detection_results.png');
echo "\n✓ Screenshot saved: /tmp/bot_detection_results.png\n";

$driver4->quit();
echo "\n";

// Summary
echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║                    STEALTH MODE SUMMARY                       ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

echo "Stealth Features:\n";
echo "  ✓ navigator.webdriver hidden (prevents detection)\n";
echo "  ✓ Chrome automation flags removed\n";
echo "  ✓ Natural-looking plugins and languages\n";
echo "  ✓ window.chrome object present\n";
echo "  ✓ Custom user agent support\n";
echo "  ✓ Automation infobars disabled\n";
echo "  ✓ Permission requests modified\n\n";

echo "Usage:\n";
echo "  // Default (stealth enabled automatically)\n";
echo "  \$driver = new WebDriver(\$path, \$port, \$capabilities);\n\n";
echo "  // Custom stealth configuration\n";
echo "  \$stealth = StealthConfig::custom(['userAgent' => '...']);\n";
echo "  \$driver = new WebDriver(\$path, \$port, \$capabilities, \$stealth);\n\n";
echo "  // Disable stealth (for testing)\n";
echo "  \$stealth = StealthConfig::disabled();\n";
echo "  \$driver = new WebDriver(\$path, \$port, \$capabilities, \$stealth);\n\n";

echo "Note: Stealth mode is ENABLED BY DEFAULT to help bypass common\n";
echo "      bot protection systems!\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
echo "Examples completed successfully!\n";
echo "═══════════════════════════════════════════════════════════════\n";
