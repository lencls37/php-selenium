<?php

require_once __DIR__ . '/vendor/autoload.php';

use Lencls37\PhpSelenium\SeleniumDriver;
use Lencls37\PhpSelenium\FirefoxDriver;
use Lencls37\PhpSelenium\EdgeDriver;

echo "=== PHP Selenium Driver Example ===\n\n";

// Example 1: Chrome/Chromium Driver
echo "1. Setting up Chrome Driver...\n";
try {
    $chromeDriver = new SeleniumDriver();
    $chromeDriver->initialize();
    
    echo "\nChrome Driver Path: " . $chromeDriver->getDriverPath() . "\n";
    echo "Chrome Binary Path: " . $chromeDriver->getChromePath() . "\n";
    echo "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// Example 2: Firefox Driver (optional)
echo "2. Setting up Firefox Driver (optional)...\n";
try {
    $firefoxDriver = new FirefoxDriver();
    $firefoxDriver->initialize();
    
    echo "\nFirefox Driver Path: " . $firefoxDriver->getDriverPath() . "\n";
    echo "Firefox Binary Path: " . $firefoxDriver->getBrowserPath() . "\n";
    echo "\n";
} catch (Exception $e) {
    echo "Note: " . $e->getMessage() . "\n\n";
}

// Example 3: Edge Driver (optional)
echo "3. Setting up Edge Driver (optional)...\n";
try {
    $edgeDriver = new EdgeDriver();
    $edgeDriver->initialize();
    
    echo "\nEdge Driver Path: " . $edgeDriver->getDriverPath() . "\n";
    echo "Edge Binary Path: " . $edgeDriver->getBrowserPath() . "\n";
    echo "\n";
} catch (Exception $e) {
    echo "Note: " . $e->getMessage() . "\n\n";
}

echo "=== Setup Complete ===\n";
echo "\nYou can now use these drivers with your Selenium tests.\n";
echo "Example usage with php-webdriver:\n";
echo "  use Facebook\\WebDriver\\Remote\\RemoteWebDriver;\n";
echo "  \$driver = RemoteWebDriver::create('http://localhost:4444', DesiredCapabilities::chrome());\n";
