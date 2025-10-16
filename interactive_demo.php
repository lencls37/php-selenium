<?php

/**
 * Interactive Demo: Shows how the Turkish prompt "Chrome indirilsin mi? (y/n)" works
 * 
 * This simulates what happens when Chrome is not found on the system.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Lencls37\PhpSelenium\SeleniumDriver;

echo "═══════════════════════════════════════════════════════════════\n";
echo "    INTERACTIVE CHROME INSTALLATION DEMO\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "This demo shows the interactive Chrome installation process.\n";
echo "When Chrome is not found, the library prompts:\n\n";

echo "  Chrome/Chromium not found on the system.\n";
echo "  Chrome indirilsin mi? (y/n): \n\n";

echo "User can respond:\n";
echo "  • 'y' → Downloads Chrome and ChromeDriver for the OS\n";
echo "  • 'n' → Cancels installation and throws exception\n\n";

echo "───────────────────────────────────────────────────────────────\n\n";

echo "In this environment, Chrome IS already installed, so you'll see:\n\n";

try {
    $driver = new SeleniumDriver();
    
    // Show what Chrome path was found
    $chromePath = $driver->getChromePath();
    if ($chromePath) {
        echo "✓ Chrome detected at: $chromePath\n\n";
        echo "The interactive prompt is SKIPPED because Chrome exists.\n\n";
    }
    
    echo "Initializing driver...\n\n";
    $driver->initialize();
    
    echo "\n✓ Setup complete!\n";
    echo "  Driver: " . $driver->getDriverPath() . "\n";
    echo "  Chrome: " . $driver->getChromePath() . "\n\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

echo "───────────────────────────────────────────────────────────────\n\n";

echo "TO SEE THE INTERACTIVE PROMPT:\n";
echo "  1. Uninstall Chrome from your system\n";
echo "  2. Run: php example.php\n";
echo "  3. You'll see: 'Chrome indirilsin mi? (y/n):'\n";
echo "  4. Type 'y' to download or 'n' to cancel\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
