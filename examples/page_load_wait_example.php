<?php

/**
 * Page Load Wait Example
 * 
 * Demonstrates how to use wait for page load methods and get HTML content
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Lencls37\PhpSelenium\ChromeDriver;
use Lencls37\PhpSelenium\WebDriver;

echo "Page Load Wait Example\n";
echo "======================\n\n";

try {
    // 1. Setup ChromeDriver
    echo "Setting up ChromeDriver...\n";
    $seleniumDriver = new ChromeDriver();
    $seleniumDriver->initialize();
    
    // 2. Start browser in headless mode
    echo "Starting browser...\n";
    $driver = new WebDriver($seleniumDriver->getDriverPath(), 9515, [
        'goog:chromeOptions' => [
            'binary' => $seleniumDriver->getChromePath(),
            'args' => ['--headless', '--disable-gpu', '--no-sandbox']
        ]
    ]);
    $driver->start();
    
    // 3. Navigate to a website
    echo "Navigating to example.com...\n";
    $driver->get('https://example.com');
    
    // 4. Wait for page to fully load
    echo "Waiting for page to fully load...\n";
    $driver->waitForPageLoad(30);
    echo "✓ Page loaded completely!\n\n";
    
    // 5. Get page information
    echo "Page Information:\n";
    echo "  Title: " . $driver->getTitle() . "\n";
    echo "  URL: " . $driver->getCurrentUrl() . "\n\n";
    
    // 6. Get HTML content using getPageSource()
    echo "Getting HTML content using getPageSource()...\n";
    $html1 = $driver->getPageSource();
    echo "  HTML length: " . strlen($html1) . " characters\n";
    echo "  First 100 chars: " . substr($html1, 0, 100) . "...\n\n";
    
    // 7. Get HTML content using getHtml() (alias)
    echo "Getting HTML content using getHtml()...\n";
    $html2 = $driver->getHtml();
    echo "  HTML length: " . strlen($html2) . " characters\n";
    echo "  First 100 chars: " . substr($html2, 0, 100) . "...\n\n";
    
    // 8. Demonstrate waitForPageReady() - wait for DOM ready
    echo "Navigating to another page...\n";
    $driver->get('https://www.wikipedia.org');
    
    echo "Waiting for DOM to be ready...\n";
    $driver->waitForPageReady(30);
    echo "✓ DOM is ready!\n\n";
    
    // 9. Wait for page to fully load
    echo "Waiting for page to fully load...\n";
    $driver->waitForPageLoad(30);
    echo "✓ Page loaded completely!\n\n";
    
    echo "Page title: " . $driver->getTitle() . "\n";
    
    // 10. Find and interact with elements after load
    echo "\nFinding elements after page load...\n";
    try {
        $links = $driver->findElementsByTagName('a');
        echo "  Found " . count($links) . " links on the page\n";
    } catch (Exception $e) {
        echo "  Note: " . $e->getMessage() . "\n";
    }
    
    // 11. Execute JavaScript to verify page state
    $readyState = $driver->executeScript('return document.readyState;');
    echo "  Document ready state: " . $readyState . "\n";
    
    // 12. Take screenshot after page load
    $driver->saveScreenshot('/tmp/page_loaded_screenshot.png');
    echo "  Screenshot saved to: /tmp/page_loaded_screenshot.png\n";
    
    echo "\n✓ Success! All page load wait features are working.\n\n";
    
    // Clean up
    $driver->quit();
    
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    if (isset($driver)) {
        $driver->quit();
    }
    exit(1);
}
