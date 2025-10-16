<?php

/**
 * Browser Control Example
 * 
 * This example demonstrates how to use the WebDriver class to control
 * the browser, find elements, interact with them, and perform various
 * automation tasks - just like Selenium!
 */

require_once __DIR__ . '/vendor/autoload.php';

use Lencls37\PhpSelenium\SeleniumDriver;
use Lencls37\PhpSelenium\WebDriver;

echo "═══════════════════════════════════════════════════════════════\n";
echo "    BROWSER CONTROL EXAMPLE - PHP SELENIUM\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

try {
    // Step 1: Initialize ChromeDriver
    echo "1. Setting up ChromeDriver...\n";
    $seleniumDriver = new SeleniumDriver();
    $seleniumDriver->initialize();
    
    $driverPath = $seleniumDriver->getDriverPath();
    $chromePath = $seleniumDriver->getChromePath();
    
    echo "   ✓ ChromeDriver: $driverPath\n";
    echo "   ✓ Chrome: $chromePath\n\n";
    
    // Step 2: Create WebDriver instance and start browser
    echo "2. Starting browser...\n";
    $driver = new WebDriver($driverPath, 9515, [
        'goog:chromeOptions' => [
            'binary' => $chromePath,
            'args' => ['--headless', '--disable-gpu', '--no-sandbox']
        ]
    ]);
    $driver->start();
    echo "   ✓ Browser started successfully\n\n";
    
    // Step 3: Navigate to a website
    echo "3. Navigating to example.com...\n";
    $driver->get('https://example.com');
    echo "   ✓ Current URL: " . $driver->getCurrentUrl() . "\n";
    echo "   ✓ Page Title: " . $driver->getTitle() . "\n\n";
    
    // Step 4: Find elements by different methods
    echo "4. Finding elements...\n";
    
    // Find by tag name
    $h1 = $driver->findElementByTagName('h1');
    echo "   ✓ H1 text: " . $h1->getText() . "\n";
    
    // Find by CSS selector
    $paragraphs = $driver->findElementsByCssSelector('p');
    echo "   ✓ Found " . count($paragraphs) . " paragraphs\n";
    
    // Find by XPath
    $link = $driver->findElementByXPath('//a[@href]');
    echo "   ✓ Link text: " . $link->getText() . "\n";
    echo "   ✓ Link href: " . $link->getAttribute('href') . "\n\n";
    
    // Step 5: Get page source
    echo "5. Getting page source...\n";
    $source = $driver->getPageSource();
    echo "   ✓ Page source length: " . strlen($source) . " characters\n\n";
    
    // Step 6: Execute JavaScript
    echo "6. Executing JavaScript...\n";
    $result = $driver->executeScript('return document.title;');
    echo "   ✓ JavaScript result: $result\n";
    
    $result = $driver->executeScript('return navigator.userAgent;');
    echo "   ✓ User Agent: $result\n\n";
    
    // Step 7: Window management
    echo "7. Window management...\n";
    $size = $driver->getWindowSize();
    echo "   ✓ Current window size: {$size['width']}x{$size['height']}\n";
    
    $driver->setWindowSize(1280, 720);
    echo "   ✓ Window resized to 1280x720\n\n";
    
    // Step 8: Take screenshot
    echo "8. Taking screenshot...\n";
    $screenshotPath = '/tmp/example_screenshot.png';
    $driver->saveScreenshot($screenshotPath);
    echo "   ✓ Screenshot saved to: $screenshotPath\n\n";
    
    // Step 9: Navigate to Google and perform a search
    echo "9. Testing Google search...\n";
    $driver->get('https://www.google.com');
    echo "   ✓ Navigated to: " . $driver->getCurrentUrl() . "\n";
    
    // Wait for search box and enter search query
    try {
        $searchBox = $driver->waitForElement('input[name="q"]', 5);
        $searchBox->sendKeys('PHP Selenium WebDriver');
        echo "   ✓ Entered search query\n";
        
        // Submit the search
        $searchBox->submit();
        sleep(2); // Wait for results
        
        echo "   ✓ Search completed\n";
        echo "   ✓ Results page title: " . $driver->getTitle() . "\n\n";
    } catch (Exception $e) {
        echo "   ⚠ Google search test skipped: " . $e->getMessage() . "\n\n";
    }
    
    // Step 10: Cookie management
    echo "10. Cookie management...\n";
    $driver->get('https://example.com');
    
    $driver->addCookie([
        'name' => 'test_cookie',
        'value' => 'test_value'
    ]);
    echo "   ✓ Cookie added\n";
    
    $cookies = $driver->getCookies();
    echo "   ✓ Total cookies: " . count($cookies) . "\n";
    
    foreach ($cookies as $cookie) {
        echo "      - {$cookie['name']}: {$cookie['value']}\n";
    }
    
    $driver->deleteCookie('test_cookie');
    echo "   ✓ Cookie deleted\n\n";
    
    // Step 11: Navigation commands
    echo "11. Testing navigation...\n";
    $driver->get('https://example.com');
    $firstUrl = $driver->getCurrentUrl();
    
    $driver->get('https://www.iana.org/domains/reserved');
    $secondUrl = $driver->getCurrentUrl();
    echo "   ✓ Navigated to: $secondUrl\n";
    
    $driver->back();
    echo "   ✓ Navigated back to: " . $driver->getCurrentUrl() . "\n";
    
    $driver->forward();
    echo "   ✓ Navigated forward to: " . $driver->getCurrentUrl() . "\n";
    
    $driver->refresh();
    echo "   ✓ Page refreshed\n\n";
    
    // Step 12: Element properties and state
    echo "12. Testing element properties...\n";
    $driver->get('https://example.com');
    
    $heading = $driver->findElementByTagName('h1');
    echo "   ✓ Tag name: " . $heading->getTagName() . "\n";
    echo "   ✓ Is displayed: " . ($heading->isDisplayed() ? 'yes' : 'no') . "\n";
    echo "   ✓ Is enabled: " . ($heading->isEnabled() ? 'yes' : 'no') . "\n";
    
    $rect = $heading->getRect();
    echo "   ✓ Element position: ({$rect['x']}, {$rect['y']})\n";
    echo "   ✓ Element size: {$rect['width']}x{$rect['height']}\n\n";
    
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "    ALL TESTS COMPLETED SUCCESSFULLY!\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";
    
    echo "Available WebDriver methods:\n";
    echo "  Navigation:\n";
    echo "    - get(url), back(), forward(), refresh()\n";
    echo "    - getCurrentUrl(), getTitle(), getPageSource()\n";
    echo "\n";
    echo "  Element Finding:\n";
    echo "    - findElementByCssSelector(selector)\n";
    echo "    - findElementsByXPath(xpath)\n";
    echo "    - findElementById(id)\n";
    echo "    - findElementByName(name)\n";
    echo "    - findElementByClassName(class)\n";
    echo "    - findElementByTagName(tag)\n";
    echo "    - findElementByLinkText(text)\n";
    echo "\n";
    echo "  Element Interaction:\n";
    echo "    - element->click()\n";
    echo "    - element->sendKeys(text)\n";
    echo "    - element->clear()\n";
    echo "    - element->submit()\n";
    echo "    - element->getText()\n";
    echo "    - element->getAttribute(name)\n";
    echo "\n";
    echo "  JavaScript:\n";
    echo "    - executeScript(script, args)\n";
    echo "    - executeAsyncScript(script, args)\n";
    echo "\n";
    echo "  Screenshots:\n";
    echo "    - takeScreenshot()\n";
    echo "    - saveScreenshot(filename)\n";
    echo "    - element->takeScreenshot()\n";
    echo "\n";
    echo "  Window Management:\n";
    echo "    - maximize(), minimize()\n";
    echo "    - setWindowSize(width, height)\n";
    echo "    - getWindowSize()\n";
    echo "\n";
    echo "  Cookies:\n";
    echo "    - addCookie(cookie)\n";
    echo "    - getCookies()\n";
    echo "    - deleteCookie(name)\n";
    echo "    - deleteAllCookies()\n";
    echo "\n";
    echo "  Waits:\n";
    echo "    - waitForElement(selector, timeout)\n";
    echo "    - waitForElementVisible(selector, timeout)\n";
    echo "    - waitUntil(condition, timeout)\n";
    echo "    - implicitWait(timeoutMs)\n";
    echo "\n";
    
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

echo "\nFor more examples, see the documentation.\n";
