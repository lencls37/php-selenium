<?php

require_once __DIR__ . '/vendor/autoload.php';

use Lencls37\PhpSelenium\ChromeDriver;
use Lencls37\PhpSelenium\GeckoDriver;

echo "=== PHP Selenium Driver Example ===\n\n";

// Example 1: Chrome/Chromium Driver
echo "1. Setting up Chrome Driver...\n";
try {
    $chromeDriver = new ChromeDriver();
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
    $firefoxDriver = new GeckoDriver();
    $firefoxDriver->initialize();
    
    echo "\nFirefox Driver Path: " . $firefoxDriver->getDriverPath() . "\n";
    echo "Firefox Binary Path: " . $firefoxDriver->getBrowserPath() . "\n";
    echo "\n";
} catch (Exception $e) {
    echo "Note: " . $e->getMessage() . "\n\n";
}

echo "=== Setup Complete ===\n";
echo "\nYou can now use these drivers with your Selenium tests.\n";
echo "Example usage with php-webdriver:\n";
echo "  use Facebook\\WebDriver\\Remote\\RemoteWebDriver;\n";
echo "  \$driver = RemoteWebDriver::create('http://localhost:4444', DesiredCapabilities::chrome());\n";
