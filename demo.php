<?php

/**
 * Demo script showing how the PHP Selenium library works
 * 
 * This demonstrates:
 * 1. Browser detection across different operating systems
 * 2. Interactive Chrome download prompt (when Chrome is not found)
 * 3. Automatic driver download and setup
 * 4. Multi-browser support (Chrome, Firefox, Edge)
 */

require_once __DIR__ . '/vendor/autoload.php';

use Lencls37\PhpSelenium\ChromeDriver;
use Lencls37\PhpSelenium\GeckoDriver;
use Lencls37\PhpSelenium\EdgeDriver;

echo "╔════════════════════════════════════════════════════════╗\n";
echo "║     PHP Selenium Driver - Interactive Demo            ║\n";
echo "╚════════════════════════════════════════════════════════╝\n\n";

function printSection($title) {
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "  $title\n";
    echo str_repeat("=", 60) . "\n\n";
}

function printSuccess($message) {
    echo "✓ $message\n";
}

function printInfo($message) {
    echo "ℹ $message\n";
}

function printWarning($message) {
    echo "⚠ $message\n";
}

// Demo 1: Chrome Driver Setup
printSection("1. CHROME DRIVER SETUP");

printInfo("Initializing Chrome/Chromium driver...");
echo "\n";

try {
    $chromeDriver = new ChromeDriver();
    
    // Get Chrome path before initialization
    $chromePath = $chromeDriver->getChromePath();
    
    if ($chromePath) {
        printSuccess("Chrome found at: $chromePath");
    } else {
        printWarning("Chrome not found on system");
        printInfo("The library would now prompt: 'Chrome indirilsin mi? (y/n)'");
        printInfo("If user selects 'y': Chrome will be downloaded for your OS");
        printInfo("If user selects 'n': Process will be cancelled");
    }
    
    echo "\nInitializing driver...\n\n";
    $chromeDriver->initialize();
    
    echo "\n";
    printSuccess("ChromeDriver path: " . $chromeDriver->getDriverPath());
    printSuccess("Chrome binary path: " . $chromeDriver->getChromePath());
    
} catch (Exception $e) {
    printWarning("Chrome setup: " . $e->getMessage());
}

// Demo 2: Firefox Driver Setup (Optional)
printSection("2. FIREFOX DRIVER SETUP (Optional)");

printInfo("Checking for Firefox installation...");
echo "\n";

try {
    $firefoxDriver = new GeckoDriver();
    $firefoxPath = $firefoxDriver->getBrowserPath();
    
    if ($firefoxPath) {
        printSuccess("Firefox found at: $firefoxPath");
        
        echo "\nInitializing GeckoDriver...\n\n";
        $firefoxDriver->initialize();
        
        echo "\n";
        printSuccess("GeckoDriver path: " . $firefoxDriver->getDriverPath());
        printSuccess("Firefox binary path: " . $firefoxDriver->getBrowserPath());
    } else {
        printInfo("Firefox not found - skipping");
    }
    
} catch (Exception $e) {
    printInfo("Firefox setup: " . $e->getMessage());
}

// Demo 3: Edge Driver Setup (Optional)
printSection("3. EDGE DRIVER SETUP (Optional)");

printInfo("Checking for Microsoft Edge installation...");
echo "\n";

try {
    $edgeDriver = new EdgeDriver();
    $edgePath = $edgeDriver->getBrowserPath();
    
    if ($edgePath) {
        printSuccess("Edge found at: $edgePath");
        
        echo "\nInitializing EdgeDriver...\n\n";
        $edgeDriver->initialize();
        
        echo "\n";
        printSuccess("EdgeDriver path: " . $edgeDriver->getDriverPath());
        printSuccess("Edge binary path: " . $edgeDriver->getBrowserPath());
    } else {
        printInfo("Edge not found - skipping");
    }
    
} catch (Exception $e) {
    printInfo("Edge setup: " . $e->getMessage());
}

// Demo 4: Usage Example
printSection("4. USAGE WITH SELENIUM");

echo "After setup, you can use the drivers with php-webdriver:\n\n";

echo "<?php\n";
echo "use Facebook\\WebDriver\\Remote\\RemoteWebDriver;\n";
echo "use Facebook\\WebDriver\\Remote\\DesiredCapabilities;\n\n";

echo "// Start ChromeDriver on port 9515\n";
echo "\$process = proc_open(\n";
echo "    [\$driver->getDriverPath(), '--port=9515'],\n";
echo "    [...]\n";
echo ");\n\n";

echo "// Connect to the driver\n";
echo "\$driver = RemoteWebDriver::create(\n";
echo "    'http://localhost:9515',\n";
echo "    DesiredCapabilities::chrome()\n";
echo ");\n\n";

echo "// Use Selenium\n";
echo "\$driver->get('https://www.google.com');\n";
echo "\$driver->getTitle(); // 'Google'\n";

printSection("5. PLATFORM SUPPORT");

echo "This library works on:\n";
echo "  ✓ Windows (x64)\n";
echo "  ✓ Linux (x64)\n";
echo "  ✓ macOS (x64)\n\n";

echo "Supported browsers:\n";
echo "  ✓ Chrome/Chromium (automatic download)\n";
echo "  ✓ Firefox (manual installation required)\n";
echo "  ✓ Microsoft Edge (manual installation required)\n";

printSection("DEMO COMPLETE");

echo "The library is ready to use!\n\n";
echo "Key features:\n";
echo "  • Automatic browser detection\n";
echo "  • Interactive installation prompts\n";
echo "  • Cross-platform compatibility\n";
echo "  • Multi-browser support\n";
echo "  • Version matching (browser <-> driver)\n\n";
