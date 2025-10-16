<?php

/**
 * Get HTML Example
 * 
 * Simple example showing how to get HTML content from a webpage
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Lencls37\PhpSelenium\ChromeDriver;
use Lencls37\PhpSelenium\WebDriver;

echo "Get HTML Content Example\n";
echo "========================\n\n";

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
    echo "Waiting for page to load...\n";
    $driver->waitForPageLoad();
    echo "✓ Page loaded!\n\n";
    
    // 5. Get HTML using getPageSource()
    echo "Method 1: Using getPageSource()\n";
    echo "--------------------------------\n";
    $html1 = $driver->getPageSource();
    echo "HTML Length: " . strlen($html1) . " characters\n";
    echo "First 200 characters:\n";
    echo substr($html1, 0, 200) . "...\n\n";
    
    // 6. Get HTML using getHtml() (alias method)
    echo "Method 2: Using getHtml() - Alias Method\n";
    echo "-----------------------------------------\n";
    $html2 = $driver->getHtml();
    echo "HTML Length: " . strlen($html2) . " characters\n";
    echo "First 200 characters:\n";
    echo substr($html2, 0, 200) . "...\n\n";
    
    // 7. Verify both methods return the same HTML
    if ($html1 === $html2) {
        echo "✓ Both methods return identical HTML content\n\n";
    } else {
        echo "⚠ Warning: Methods returned different content\n\n";
    }
    
    // 8. Extract specific information from HTML
    echo "Extracting Information from HTML\n";
    echo "--------------------------------\n";
    
    // Count number of tags
    $tagCount = substr_count($html1, '<');
    echo "Number of HTML tags: {$tagCount}\n";
    
    // Check if specific tags exist
    $hasTitle = strpos($html1, '<title>') !== false;
    $hasBody = strpos($html1, '<body>') !== false;
    $hasHead = strpos($html1, '<head>') !== false;
    
    echo "Contains <title> tag: " . ($hasTitle ? "Yes" : "No") . "\n";
    echo "Contains <head> tag: " . ($hasHead ? "Yes" : "No") . "\n";
    echo "Contains <body> tag: " . ($hasBody ? "Yes" : "No") . "\n\n";
    
    // 9. Save HTML to file
    $htmlFile = '/tmp/example_page.html';
    file_put_contents($htmlFile, $html1);
    echo "✓ HTML saved to: {$htmlFile}\n";
    echo "File size: " . filesize($htmlFile) . " bytes\n\n";
    
    // 10. Get page info for comparison
    echo "Additional Page Information\n";
    echo "---------------------------\n";
    echo "Page Title: " . $driver->getTitle() . "\n";
    echo "Page URL: " . $driver->getCurrentUrl() . "\n";
    echo "Document Ready State: " . $driver->executeScript('return document.readyState;') . "\n\n";
    
    echo "✓ Success! HTML content retrieved successfully.\n\n";
    
    // Clean up
    $driver->quit();
    
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    if (isset($driver)) {
        $driver->quit();
    }
    exit(1);
}
