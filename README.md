# PHP Selenium Driver

Selenium PHP library with automatic Chrome/ChromeDriver setup and multi-browser support.

## Features

- 🚀 Automatic Chrome/Chromium detection and installation
- 📦 Automatic ChromeDriver download and setup
- 🌐 Cross-platform support (Windows, Linux, macOS)
- 🔧 Firefox/Gecko driver support
- 💬 Interactive installation prompts
- ⚡ Version matching between browser and driver
- 🎮 **Full browser automation** - Control the browser like Selenium
- 🔍 **Element finding** - CSS selectors, XPath, ID, name, class, etc.
- 🖱️ **Element interaction** - Click, type, submit forms, get properties
- 📸 **Screenshots** - Full page and element screenshots
- 🍪 **Cookie management** - Add, get, delete cookies
- 🪟 **Window & Frame control** - Resize, maximize, switch windows/frames
- 🚨 **Alert handling** - Accept, dismiss, get text from alerts
- ⌨️ **Actions API** - Advanced keyboard and mouse interactions
- ⏱️ **Smart waits & Timeouts** - Wait for elements, conditions, and configure timeouts
- 🎯 **JavaScript execution** - Run custom scripts (sync & async)
- 🔍 **Session management** - Get status, list sessions, manage capabilities
- 🥷 **Stealth mode** - Hide Selenium from bot detection (enabled by default)

## Requirements

- PHP 8.0 or higher
- ext-zip
- ext-json
- Composer

## Installation

```bash
composer require lencls37/php-selenium
```

Or clone this repository and run:

```bash
composer install
```

## Usage

### Quick Start - Browser Automation

```php
<?php

require_once 'vendor/autoload.php';

use Lencls37\PhpSelenium\ChromeDriver;
use Lencls37\PhpSelenium\WebDriver;

// 1. Setup ChromeDriver
$seleniumDriver = new ChromeDriver();
$seleniumDriver->initialize();

// 2. Start browser
$driver = new WebDriver($seleniumDriver->getDriverPath(), 9515, [
    'goog:chromeOptions' => [
        'binary' => $seleniumDriver->getChromePath(),
        'args' => ['--headless']  // Run in headless mode
    ]
]);
$driver->start();

// 3. Navigate and interact
$driver->get('https://example.com');
echo $driver->getTitle() . "\n";

// 4. Find and interact with elements
$heading = $driver->findElementByCssSelector('h1');
echo $heading->getText() . "\n";

// 5. Take screenshot
$driver->saveScreenshot('screenshot.png');

// 6. Clean up
$driver->quit();
```

### Basic Chrome Setup (Driver Only)

```php
<?php

require_once 'vendor/autoload.php';

use Lencls37\PhpSelenium\ChromeDriver;

// Initialize Chrome driver
$driver = new ChromeDriver();
$driver->initialize();

// Get driver and browser paths
$driverPath = $driver->getDriverPath();
$chromePath = $driver->getChromePath();

echo "ChromeDriver: $driverPath\n";
echo "Chrome: $chromePath\n";
```

### Firefox Support

```php
<?php

use Lencls37\PhpSelenium\GeckoDriver;

$firefoxDriver = new GeckoDriver();
$firefoxDriver->initialize();

$driverPath = $firefoxDriver->getDriverPath();
$firefoxPath = $firefoxDriver->getBrowserPath();
```

## Browser Automation

### Finding Elements

```php
// By CSS Selector
$element = $driver->findElementByCssSelector('#myId');
$elements = $driver->findElementsByCssSelector('.myClass');

// By XPath
$element = $driver->findElementByXPath('//div[@class="content"]');
$elements = $driver->findElementsByXPath('//a[@href]');

// By ID, Name, Class, Tag
$element = $driver->findElementById('myId');
$element = $driver->findElementByName('username');
$element = $driver->findElementByClassName('btn');
$elements = $driver->findElementsByTagName('a');

// By Link Text
$link = $driver->findElementByLinkText('Click here');
$link = $driver->findElementByPartialLinkText('Click');
```

### Interacting with Elements

```php
// Click an element
$button = $driver->findElementById('submit');
$button->click();

// Type text
$input = $driver->findElementByName('username');
$input->sendKeys('myusername');

// Clear input field
$input->clear();

// Submit form
$input->submit();

// Get element text and attributes
$text = $element->getText();
$href = $link->getAttribute('href');
$cssValue = $element->getCssValue('color');

// Check element state
if ($element->isDisplayed()) {
    echo "Element is visible\n";
}
if ($element->isEnabled()) {
    echo "Element is enabled\n";
}
if ($checkbox->isSelected()) {
    echo "Checkbox is checked\n";
}
```

### Navigation

```php
// Navigate to URL
$driver->get('https://example.com');

// Get current URL and title
echo $driver->getCurrentUrl() . "\n";
echo $driver->getTitle() . "\n";

// Navigation commands
$driver->back();
$driver->forward();
$driver->refresh();

// Get page source / HTML code
$html = $driver->getPageSource();
// Or use the alias
$html = $driver->getHtml();
```

### JavaScript Execution

```php
// Execute JavaScript
$result = $driver->executeScript('return document.title;');

// Execute with arguments
$result = $driver->executeScript(
    'return arguments[0] + arguments[1];',
    [5, 10]
); // Returns 15

// Scroll to element
$driver->executeScript('arguments[0].scrollIntoView();', [$element->toArray()]);
```

### Screenshots

```php
// Take full page screenshot
$driver->saveScreenshot('page.png');

// Get base64 encoded screenshot
$base64 = $driver->takeScreenshot();

// Take element screenshot
$element->saveScreenshot('element.png');
```

### Window Management

```php
// Maximize window
$driver->maximize();

// Minimize window
$driver->minimize();

// Set custom size
$driver->setWindowSize(1920, 1080);

// Get window size
$size = $driver->getWindowSize();
echo "Width: {$size['width']}, Height: {$size['height']}\n";
```

### Cookie Management

```php
// Add cookie
$driver->addCookie([
    'name' => 'session',
    'value' => 'abc123'
]);

// Get all cookies
$cookies = $driver->getCookies();
foreach ($cookies as $cookie) {
    echo "{$cookie['name']}: {$cookie['value']}\n";
}

// Delete specific cookie
$driver->deleteCookie('session');

// Delete all cookies
$driver->deleteAllCookies();
```

### Waits

```php
// Wait for page to fully load (document.readyState === 'complete')
$driver->waitForPageLoad(30); // Wait up to 30 seconds

// Wait for DOM to be ready (document.readyState === 'interactive' or 'complete')
$driver->waitForPageReady(30);

// Wait for jQuery/AJAX requests to complete
$driver->waitForAjax(30);

// Wait for element to be present
$element = $driver->waitForElement('#dynamic-content', 10);

// Wait for element to be visible
$element = $driver->waitForElementVisible('#loading', 5);

// Wait for custom condition
$driver->waitUntil(function($driver) {
    return $driver->getTitle() === 'Expected Title';
}, 10);

// Set implicit wait
$driver->implicitWait(5000); // 5 seconds in milliseconds
```

### Advanced Element Finding

```php
// Find child elements
$parent = $driver->findElementById('parent');
$child = $parent->findElementByCssSelector('.child');
$children = $parent->findElementsByTagName('li');

// Get element location and size
$location = $element->getLocation();
echo "X: {$location['x']}, Y: {$location['y']}\n";

$size = $element->getSize();
echo "Width: {$size['width']}, Height: {$size['height']}\n";

$rect = $element->getRect();
// Returns: ['x' => ..., 'y' => ..., 'width' => ..., 'height' => ...]
```

### Stealth Mode (Anti-Bot Detection)

**Stealth mode is ENABLED BY DEFAULT** to help bypass bot protection systems:

```php
use Lencls37\PhpSelenium\StealthConfig;

// Default usage - stealth is already enabled!
$driver = new WebDriver($driverPath, 9515, $capabilities);

// Custom stealth configuration
$stealth = StealthConfig::custom([
    'hideWebdriver' => true,      // Hide navigator.webdriver
    'hideAutomation' => true,     // Remove automation flags
    'userAgent' => 'Custom UA',   // Custom user agent
]);
$driver = new WebDriver($driverPath, 9515, $capabilities, $stealth);

// Disable stealth (for testing)
$stealth = StealthConfig::disabled();
$driver = new WebDriver($driverPath, 9515, $capabilities, $stealth);
```

**Stealth features:**
- ✓ Hides `navigator.webdriver` property
- ✓ Removes Chrome automation flags  
- ✓ Disables "Chrome is being controlled" infobar
- ✓ Adds natural-looking plugins and languages
- ✓ Modifies permission requests
- ✓ Custom user agent support

## WebDriver Protocol Support

This library implements the complete W3C WebDriver protocol, including:
- Session management (create, list, close sessions)
- Navigation (navigate, back, forward, refresh)
- Element finding (CSS selectors, XPath, ID, name, class, tag, link text)
- Element interaction (click, type, clear, submit)
- JavaScript execution (sync and async)
- Screenshots (full page and element)
- Window management (resize, maximize, minimize)
- Cookie management (add, get, delete)
- Frame and window switching
- Alert handling
- Timeouts and waits

For more details, see the [W3C WebDriver Specification](https://www.w3.org/TR/webdriver/).

## How It Works

1. **Chrome Detection**: The library first checks if Chrome/Chromium is installed on your system
2. **Interactive Prompt**: If not found, it asks: "Chrome indirilsin mi? (y/n)"
3. **Automatic Download**: If you answer 'y', it downloads Chrome for your OS
4. **Driver Setup**: Downloads the matching ChromeDriver version
5. **Ready to Use**: Returns paths to both browser and driver

## Supported Platforms

- ✅ Windows (x64)
- ✅ Linux (x64)
- ✅ macOS (x64)

## Directory Structure

```
php-selenium/
├── src/
│   ├── ChromeDriver.php      # Chrome driver class
│   ├── GeckoDriver.php       # Firefox/Gecko driver class
│   ├── BrowserDriver.php     # Abstract base class
│   ├── WebDriver.php         # WebDriver implementation
│   └── WebElement.php        # WebElement implementation
├── drivers/                  # Downloaded drivers (auto-created)
├── chrome/                   # Downloaded Chrome (auto-created)
├── vendor/                   # Composer dependencies
├── composer.json
├── example.php              # Usage example
└── README.md

```

## Examples

### Quick Start Example
```bash
php quick_start.php
```
Simple example showing basic browser automation.

### Comprehensive Browser Control Example
```bash
php browser_control_example.php
```
Demonstrates all browser control features including:
- Element finding (CSS, XPath, ID, etc.)
- Element interaction (click, type, etc.)
- JavaScript execution
- Screenshots
- Cookie management
- Window control
- Navigation
- Waits

### Practical Examples

**Form Filling:**
```bash
php examples/form_filling_example.php
```
Shows how to fill and submit forms.

**Web Scraping:**
```bash
php examples/web_scraping_example.php
```
Demonstrates data extraction from web pages.

**Get HTML Content:**
```bash
php examples/get_html_example.php
```
Shows how to retrieve HTML content from web pages.

**Stealth Mode:**
```bash
php examples/stealth_mode_example.php
```
Shows how to bypass bot detection with stealth mode (enabled by default).

**Page Load Wait:**
```bash
php examples/page_load_wait_example.php
```
Demonstrates how to wait for page load, get HTML content, and use various wait methods.

See the [examples directory](examples/) for more examples.

### Driver Setup Example
```bash
php example.php
```
Shows how to set up ChromeDriver:
1. Check for Chrome installation
2. Prompt for download if needed
3. Set up ChromeDriver
4. Display paths to driver and browser

## Chrome Version Compatibility

The library automatically:
- Detects your Chrome version
- Downloads the matching ChromeDriver version
- Supports both legacy and new Chrome for Testing API

## Error Handling

```php
try {
    $driver = new ChromeDriver();
    $driver->initialize();
} catch (RuntimeException $e) {
    echo "Error: " . $e->getMessage();
}
```

Common errors:
- Chrome not found and user declined installation
- Failed to download Chrome or ChromeDriver
- Unsupported operating system

## License

MIT

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Author

lencls37
