# Browser Automation Guide

## Overview

This library now provides full browser automation capabilities, allowing you to control web browsers programmatically using PHP - just like Selenium WebDriver!

## What's New

The library has been enhanced with two powerful classes:

### 1. WebDriver Class
The main class for browser control and automation. It manages the browser session and provides methods for:
- Navigation
- Element finding
- JavaScript execution
- Screenshots
- Window management
- Cookie management
- Waiting for elements

### 2. WebElement Class
Represents DOM elements and provides methods for:
- Element interaction (click, type, etc.)
- Getting element properties
- Checking element state
- Finding child elements

## Quick Start

```php
<?php

require_once 'vendor/autoload.php';

use Lencls37\PhpSelenium\SeleniumDriver;
use Lencls37\PhpSelenium\WebDriver;

// Setup ChromeDriver
$seleniumDriver = new SeleniumDriver();
$seleniumDriver->initialize();

// Start browser
$driver = new WebDriver($seleniumDriver->getDriverPath(), 9515, [
    'goog:chromeOptions' => [
        'binary' => $seleniumDriver->getChromePath(),
        'args' => ['--headless']
    ]
]);
$driver->start();

// Navigate and interact
$driver->get('https://example.com');
$heading = $driver->findElementByCssSelector('h1');
echo $heading->getText() . "\n";

// Clean up
$driver->quit();
```

## Core Capabilities

### 1. Finding Elements

**CSS Selectors:**
```php
$element = $driver->findElementByCssSelector('#myId');
$elements = $driver->findElementsByCssSelector('.myClass');
```

**XPath:**
```php
$element = $driver->findElementByXPath('//div[@class="content"]');
$elements = $driver->findElementsByXPath('//a[@href]');
```

**Other Locators:**
```php
$driver->findElementById('myId');
$driver->findElementByName('username');
$driver->findElementByClassName('btn');
$driver->findElementByTagName('h1');
$driver->findElementByLinkText('Click here');
```

### 2. Element Interaction

```php
// Click
$button->click();

// Type text
$input->sendKeys('Hello World');

// Clear input
$input->clear();

// Submit form
$input->submit();

// Get text and attributes
$text = $element->getText();
$href = $link->getAttribute('href');
$value = $element->getCssValue('color');
```

### 3. Element State

```php
// Check visibility
if ($element->isDisplayed()) {
    echo "Element is visible\n";
}

// Check if enabled
if ($button->isEnabled()) {
    $button->click();
}

// Check if selected (checkbox/radio)
if ($checkbox->isSelected()) {
    echo "Checkbox is checked\n";
}

// Get position and size
$location = $element->getLocation(); // ['x' => ..., 'y' => ...]
$size = $element->getSize();         // ['width' => ..., 'height' => ...]
$rect = $element->getRect();         // All in one
```

### 4. Navigation

```php
// Navigate to URL
$driver->get('https://example.com');

// Get current URL and title
$url = $driver->getCurrentUrl();
$title = $driver->getTitle();

// Browser controls
$driver->back();
$driver->forward();
$driver->refresh();

// Get page source
$html = $driver->getPageSource();
```

### 5. JavaScript Execution

```php
// Execute JavaScript
$title = $driver->executeScript('return document.title;');

// With arguments
$result = $driver->executeScript(
    'return arguments[0] + arguments[1];',
    [5, 10]
);

// Async JavaScript
$driver->executeAsyncScript('
    var callback = arguments[arguments.length - 1];
    setTimeout(function() {
        callback("done");
    }, 1000);
');
```

### 6. Screenshots

```php
// Full page screenshot
$driver->saveScreenshot('page.png');

// Get as base64
$base64 = $driver->takeScreenshot();

// Element screenshot
$element->saveScreenshot('element.png');
```

### 7. Window Management

```php
// Maximize/minimize
$driver->maximize();
$driver->minimize();

// Set custom size
$driver->setWindowSize(1920, 1080);

// Get window size
$size = $driver->getWindowSize();
```

### 8. Cookie Management

```php
// Add cookie
$driver->addCookie([
    'name' => 'session',
    'value' => 'abc123'
]);

// Get all cookies
$cookies = $driver->getCookies();

// Delete cookies
$driver->deleteCookie('session');
$driver->deleteAllCookies();
```

### 9. Waits

```php
// Wait for element to appear
$element = $driver->waitForElement('#dynamic', 10);

// Wait for element to be visible
$element = $driver->waitForElementVisible('#loading', 5);

// Wait for custom condition
$driver->waitUntil(function($driver) {
    return $driver->getTitle() === 'Expected Title';
}, 10);

// Set implicit wait
$driver->implicitWait(5000); // milliseconds
```

## Common Use Cases

### Login Automation

```php
$driver->get('https://example.com/login');
$driver->findElementById('username')->sendKeys('user@example.com');
$driver->findElementById('password')->sendKeys('password123');
$driver->findElementByCssSelector('button[type="submit"]')->click();
$driver->waitUntil(function($d) {
    return str_contains($d->getCurrentUrl(), '/dashboard');
}, 10);
```

### Form Filling

```php
$driver->get('https://example.com/form');
$driver->findElementByName('firstname')->sendKeys('John');
$driver->findElementByName('lastname')->sendKeys('Doe');
$driver->findElementById('terms')->click(); // checkbox
$driver->findElementByCssSelector('button.submit')->click();
```

### Web Scraping

```php
$driver->get('https://example.com/products');
$products = $driver->findElementsByCssSelector('.product');

foreach ($products as $product) {
    $name = $product->findElementByCssSelector('.name')->getText();
    $price = $product->findElementByCssSelector('.price')->getText();
    echo "$name: $price\n";
}
```

### Dynamic Content

```php
$driver->get('https://example.com/dynamic');
$element = $driver->waitForElement('.dynamic-content', 10);
$text = $element->getText();
```

### JavaScript-Heavy Sites

```php
$driver->get('https://example.com/spa');
// Wait for JavaScript to load
$driver->waitForElement('#app', 10);
// Execute JavaScript to get data
$data = $driver->executeScript('return window.appData;');
```

## Browser Options

### Stealth Mode (Anti-Bot Detection)

**IMPORTANT: Stealth mode is ENABLED BY DEFAULT!**

Stealth mode helps bypass bot detection systems by hiding automation markers:

```php
use Lencls37\PhpSelenium\StealthConfig;

// Default - stealth is already enabled
$driver = new WebDriver($driverPath, 9515, $capabilities);

// Custom stealth configuration
$stealth = StealthConfig::custom([
    'hideWebdriver' => true,      // Hide navigator.webdriver
    'hideAutomation' => true,     // Remove automation flags
    'userAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)...',
    'disableInfobars' => true,    // Remove automation infobar
]);
$driver = new WebDriver($driverPath, 9515, $capabilities, $stealth);

// Disable stealth (for testing)
$stealth = StealthConfig::disabled();
$driver = new WebDriver($driverPath, 9515, $capabilities, $stealth);
```

**What stealth mode does:**
- Hides `navigator.webdriver` property
- Removes `--enable-automation` flag
- Adds `window.chrome` object
- Sets natural-looking plugins and languages
- Modifies permission request behavior
- Removes "Chrome is being controlled" infobar

### Headless Mode
```php
$driver = new WebDriver($driverPath, 9515, [
    'goog:chromeOptions' => [
        'binary' => $chromePath,
        'args' => ['--headless', '--disable-gpu']
    ]
]);
```

### Custom Window Size
```php
$driver = new WebDriver($driverPath, 9515, [
    'goog:chromeOptions' => [
        'binary' => $chromePath,
        'args' => ['--window-size=1920,1080']
    ]
]);
```

### Disable Images (Faster Loading)
```php
$driver = new WebDriver($driverPath, 9515, [
    'goog:chromeOptions' => [
        'binary' => $chromePath,
        'args' => ['--blink-settings=imagesEnabled=false']
    ]
]);
```

### User Agent (Manual)
```php
// Note: Use StealthConfig for better stealth
$driver = new WebDriver($driverPath, 9515, [
    'goog:chromeOptions' => [
        'binary' => $chromePath,
        'args' => ['--user-agent=Mozilla/5.0 Custom Agent']
    ]
]);
```

## Best Practices

### 1. Always Use try-finally

```php
$driver = new WebDriver(...);
$driver->start();

try {
    // Your automation code
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    $driver->quit(); // Always clean up
}
```

### 2. Use Waits for Dynamic Content

```php
// Bad - may fail if element not ready
$element = $driver->findElementById('dynamic');

// Good - waits for element
$element = $driver->waitForElement('#dynamic', 10);
```

### 3. Handle Exceptions

```php
try {
    $element = $driver->findElementByCssSelector('#missing');
} catch (RuntimeException $e) {
    echo "Element not found\n";
}
```

### 4. Take Screenshots for Debugging

```php
try {
    // automation code
} catch (Exception $e) {
    $driver->saveScreenshot('error.png');
    throw $e;
}
```

### 5. Use Descriptive Selectors

```php
// Bad - fragile
$button = $driver->findElementByXPath('//div[3]/button[2]');

// Good - descriptive
$button = $driver->findElementByCssSelector('#submitButton');
```

## Examples

See the following files for complete examples:
- `quick_start.php` - Simple getting started
- `browser_control_example.php` - Comprehensive demo
- `examples/form_filling_example.php` - Form automation
- `examples/web_scraping_example.php` - Data extraction

## API Reference

### WebDriver Methods

**Session:**
- `start()` - Start browser session
- `quit()` - Close browser and end session
- `close()` - Close current window

**Navigation:**
- `get(string $url)` - Navigate to URL
- `getCurrentUrl()` - Get current URL
- `getTitle()` - Get page title
- `getPageSource()` - Get HTML source
- `back()` - Go back
- `forward()` - Go forward
- `refresh()` - Reload page

**Element Finding:**
- `findElementByCssSelector(string $selector)`
- `findElementsByCssSelector(string $selector)`
- `findElementByXPath(string $xpath)`
- `findElementsByXPath(string $xpath)`
- `findElementById(string $id)`
- `findElementByName(string $name)`
- `findElementsByName(string $name)`
- `findElementByClassName(string $class)`
- `findElementsByClassName(string $class)`
- `findElementByTagName(string $tag)`
- `findElementsByTagName(string $tag)`
- `findElementByLinkText(string $text)`
- `findElementByPartialLinkText(string $text)`

**JavaScript:**
- `executeScript(string $script, array $args = [])`
- `executeAsyncScript(string $script, array $args = [])`

**Screenshots:**
- `takeScreenshot()` - Returns base64
- `saveScreenshot(string $filename)`

**Window:**
- `maximize()`
- `minimize()`
- `setWindowSize(int $width, int $height)`
- `getWindowSize()`

**Cookies:**
- `addCookie(array $cookie)`
- `getCookies()`
- `deleteCookie(string $name)`
- `deleteAllCookies()`

**Waits:**
- `waitForElement(string $selector, int $timeout = 10)`
- `waitForElementVisible(string $selector, int $timeout = 10)`
- `waitUntil(callable $condition, int $timeout = 10)`
- `implicitWait(int $timeoutMs)`

### WebElement Methods

**Interaction:**
- `click()`
- `sendKeys(string $text)`
- `clear()`
- `submit()`

**Information:**
- `getText()`
- `getAttribute(string $name)`
- `getCssValue(string $name)`
- `getTagName()`

**State:**
- `isDisplayed()`
- `isEnabled()`
- `isSelected()`

**Position/Size:**
- `getLocation()` - Returns ['x' => int, 'y' => int]
- `getSize()` - Returns ['width' => int, 'height' => int]
- `getRect()` - Returns all dimensions

**Screenshots:**
- `takeScreenshot()` - Returns base64
- `saveScreenshot(string $filename)`

**Child Elements:**
- `findElementByCssSelector(string $selector)`
- `findElementsByCssSelector(string $selector)`
- `findElementByXPath(string $xpath)`
- `findElementsByXPath(string $xpath)`
- `findElementByTagName(string $tag)`
- `findElementsByTagName(string $tag)`

## Troubleshooting

### Element Not Found
- Use `waitForElement()` instead of `findElement()`
- Check selector in browser DevTools
- Verify element is not in an iframe
- Check if JavaScript has loaded the element

### Timeout Issues
- Increase timeout: `$driver->waitForElement($sel, 30)`
- Check network speed
- Verify page loads correctly

### WebDriver Connection Issues
- Ensure ChromeDriver and Chrome versions match
- Check if port is available (default: 9515)
- Try different port: `new WebDriver($path, 9516)`

### Element Not Clickable
- Wait for element to be visible: `waitForElementVisible()`
- Scroll to element: `executeScript('arguments[0].scrollIntoView();', [$el->toArray()])`
- Check if element is covered by another element

## Support

For issues and questions:
- GitHub Issues: https://github.com/lencls37/php-selenium/issues
- Documentation: [README.md](README.md), [USAGE.md](USAGE.md)
- Examples: See `examples/` directory

## Turkish Summary

Bu kütüphane artık tam tarayıcı otomasyonu yeteneklerine sahip! Selenium ile yapabileceğiniz her şeyi PHP ile yapabilirsiniz:

- ✅ Element bulma (CSS Selector, XPath, ID, vb.)
- ✅ Element etkileşimi (tıklama, yazma, vb.)
- ✅ JavaScript çalıştırma
- ✅ Ekran görüntüleri
- ✅ Cookie yönetimi
- ✅ Pencere kontrolleri
- ✅ Bekleme fonksiyonları

Hepsi hazır ve kullanıma hazır! 🎉
