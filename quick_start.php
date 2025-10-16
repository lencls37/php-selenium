<?php

/**
 * Quick Start Example - Browser Automation
 * 
 * Simple example showing basic browser automation with PHP Selenium
 */

require_once __DIR__ . '/vendor/autoload.php';

use Lencls37\PhpSelenium\SeleniumDriver;
use Lencls37\PhpSelenium\WebDriver;

echo "Quick Start - Browser Automation Example\n";
echo "=========================================\n\n";

try {
    // 1. Setup ChromeDriver
    echo "Setting up ChromeDriver...\n";
    $seleniumDriver = new SeleniumDriver();
    $seleniumDriver->initialize();
    
    // 2. Start browser (stealth mode is enabled by default!)
    echo "Starting browser with stealth mode...\n";
    $driver = new WebDriver($seleniumDriver->getDriverPath(), 9515, [
        'goog:chromeOptions' => [
            'binary' => $seleniumDriver->getChromePath(),
            'args' => ['--headless', '--disable-gpu', '--no-sandbox']
        ]
    ]);
    $driver->start();
    
    // 3. Navigate to website
    echo "Navigating to example.com...\n";
    $driver->get('https://example.com');
    
    // 4. Get page information
    echo "\nPage Information:\n";
    echo "  Title: " . $driver->getTitle() . "\n";
    echo "  URL: " . $driver->getCurrentUrl() . "\n";
    
    // 5. Find elements
    echo "\nFinding elements:\n";
    $h1 = $driver->findElementByCssSelector('h1');
    echo "  H1 Text: " . $h1->getText() . "\n";
    
    $links = $driver->findElementsByTagName('a');
    echo "  Number of links: " . count($links) . "\n";
    
    // 6. Execute JavaScript
    $jsResult = $driver->executeScript('return document.body.innerText;');
    echo "  Body text length: " . strlen($jsResult) . " characters\n";
    
    // 7. Take screenshot
    $driver->saveScreenshot('/tmp/quick_start_screenshot.png');
    echo "\nScreenshot saved to: /tmp/quick_start_screenshot.png\n";
    
    echo "\n✓ Success! Browser automation is working.\n\n";
    
    // Clean up
    $driver->quit();
    
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    if (isset($driver)) {
        $driver->quit();
    }
    exit(1);
}
