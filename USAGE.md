# Usage Guide

## Quick Start

### Installation

```bash
composer require lencls37/php-selenium
```

### Quick Start - Browser Automation

```php
<?php

require_once 'vendor/autoload.php';

use Lencls37\PhpSelenium\SeleniumDriver;
use Lencls37\PhpSelenium\WebDriver;

// Setup and start browser
$seleniumDriver = new SeleniumDriver();
$seleniumDriver->initialize();

$driver = new WebDriver($seleniumDriver->getDriverPath(), 9515, [
    'goog:chromeOptions' => [
        'binary' => $seleniumDriver->getChromePath(),
        'args' => ['--headless']
    ]
]);
$driver->start();

// Navigate and interact
$driver->get('https://example.com');
echo $driver->getTitle() . "\n";

$heading = $driver->findElementByCssSelector('h1');
echo $heading->getText() . "\n";

$driver->quit();
```

### Basic Driver Setup Only

```php
<?php

require_once 'vendor/autoload.php';

use Lencls37\PhpSelenium\SeleniumDriver;

$driver = new SeleniumDriver();
$driver->initialize();

echo "Driver: " . $driver->getDriverPath() . "\n";
echo "Chrome: " . $driver->getChromePath() . "\n";
```

## Interactive Chrome Installation

When Chrome is not detected on your system, the library will prompt:

```
Chrome/Chromium not found on the system.
Chrome indirilsin mi? (y/n):
```

### Scenario 1: User answers 'y' (yes)

The library will:
1. Detect your operating system (Windows/Linux/macOS)
2. Download Chrome for Testing for your OS
3. Extract and install Chrome
4. Download the matching ChromeDriver
5. Set up everything automatically

Example output:
```
Chrome indirilsin mi? (y/n): y
Downloading Chrome for linux...
Downloading from: https://...
Progress: 100.00%
Chrome downloaded successfully.
Chrome version detected: 140.0.7339.207
Downloading ChromeDriver for Chrome version 140.0.7339.207...
ChromeDriver version: 140.0.7339.207
Downloading from: https://...
Progress: 100.00%
Extracting ChromeDriver...
ChromeDriver installed successfully at: /path/to/drivers/chromedriver
ChromeDriver setup completed successfully.
```

### Scenario 2: User answers 'n' (no)

The library will:
1. Stop the installation process
2. Throw a RuntimeException with message: "Chrome installation cancelled by user."

Example:
```
Chrome indirilsin mi? (y/n): n
RuntimeException: Chrome installation cancelled by user.
```

## Multi-Browser Support

### Chrome/Chromium

```php
use Lencls37\PhpSelenium\SeleniumDriver;

$driver = new SeleniumDriver();
$driver->initialize();
```

Features:
- Automatic detection in standard locations
- Interactive download prompt
- Automatic driver download
- Version matching

### Firefox

```php
use Lencls37\PhpSelenium\FirefoxDriver;

$driver = new FirefoxDriver();
$driver->initialize();
```

Note: Firefox must be installed manually. Download from https://www.mozilla.org/firefox/

### Microsoft Edge

```php
use Lencls37\PhpSelenium\EdgeDriver;

$driver = new EdgeDriver();
$driver->initialize();
```

Note: Edge must be installed manually. Download from https://www.microsoft.com/edge/

## Platform-Specific Behavior

### Windows

Chrome detection paths:
- `C:/Program Files/Google/Chrome/Application/chrome.exe`
- `C:/Program Files (x86)/Google/Chrome/Application/chrome.exe`
- `%LOCALAPPDATA%/Google/Chrome/Application/chrome.exe`

Downloads:
- Chrome for Testing (win64)
- ChromeDriver (win64)

### Linux

Chrome detection paths:
- `/usr/bin/google-chrome`
- `/usr/bin/google-chrome-stable`
- `/usr/bin/chromium`
- `/usr/bin/chromium-browser`
- `/snap/bin/chromium`

Downloads:
- Chrome for Testing (linux64) as .deb package
- ChromeDriver (linux64)

### macOS

Chrome detection paths:
- `/Applications/Google Chrome.app/Contents/MacOS/Google Chrome`
- `/Applications/Chromium.app/Contents/MacOS/Chromium`

Downloads:
- Chrome for Testing (mac-x64)
- ChromeDriver (mac-x64)

## Version Matching

The library automatically matches ChromeDriver versions with your Chrome version:

- **Chrome 115+**: Uses Chrome for Testing API
- **Chrome <115**: Uses legacy ChromeDriver API

Example version detection:
```
Chrome version detected: 140.0.7339.207
ChromeDriver version: 140.0.7339.207
```

## Directory Structure

After initialization, your project will have:

```
your-project/
├── vendor/
│   └── lencls37/php-selenium/
├── drivers/
│   ├── chromedriver       # or chromedriver.exe on Windows
│   ├── geckodriver        # if Firefox driver installed
│   └── msedgedriver       # if Edge driver installed
├── chrome/                # if Chrome was downloaded
│   └── chrome             # or chrome.exe on Windows
└── composer.json
```

## Error Handling

### Chrome Not Found

```php
try {
    $driver = new SeleniumDriver();
    $driver->initialize();
} catch (RuntimeException $e) {
    if (str_contains($e->getMessage(), 'cancelled by user')) {
        echo "User declined Chrome installation\n";
    }
}
```

### Network Errors

```php
try {
    $driver = new SeleniumDriver();
    $driver->initialize();
} catch (RuntimeException $e) {
    if (str_contains($e->getMessage(), 'Could not find matching ChromeDriver')) {
        echo "Failed to download ChromeDriver - check internet connection\n";
    }
}
```

### Browser Not Found

```php
try {
    $firefoxDriver = new FirefoxDriver();
    $firefoxDriver->initialize();
} catch (RuntimeException $e) {
    if (str_contains($e->getMessage(), 'not found')) {
        echo "Firefox not installed - please install manually\n";
    }
}
```

## Browser Automation Guide

### Finding Elements

The library provides multiple ways to find elements on a page:

#### By CSS Selector
```php
// Find single element
$button = $driver->findElementByCssSelector('#submitBtn');
$firstLink = $driver->findElementByCssSelector('a.external');

// Find multiple elements
$links = $driver->findElementsByCssSelector('a');
$items = $driver->findElementsByCssSelector('.list-item');
```

#### By XPath
```php
// Find by XPath
$element = $driver->findElementByXPath('//div[@class="content"]');
$links = $driver->findElementsByXPath('//a[@href]');

// Complex XPath
$button = $driver->findElementByXPath('//button[contains(text(), "Submit")]');
```

#### By ID
```php
$element = $driver->findElementById('myId');
// Equivalent to: findElementByCssSelector('#myId')
```

#### By Name
```php
$input = $driver->findElementByName('username');
$inputs = $driver->findElementsByName('category');
```

#### By Class Name
```php
$element = $driver->findElementByClassName('btn-primary');
$elements = $driver->findElementsByClassName('product');
```

#### By Tag Name
```php
$heading = $driver->findElementByTagName('h1');
$paragraphs = $driver->findElementsByTagName('p');
```

#### By Link Text
```php
// Exact match
$link = $driver->findElementByLinkText('Click here');

// Partial match
$link = $driver->findElementByPartialLinkText('Click');
```

### Element Interaction

#### Click
```php
$button = $driver->findElementById('submit');
$button->click();
```

#### Type Text (Send Keys)
```php
$input = $driver->findElementByName('username');
$input->sendKeys('myusername');

$password = $driver->findElementByName('password');
$password->sendKeys('mypassword');
```

#### Clear Input
```php
$input = $driver->findElementByName('search');
$input->clear();
$input->sendKeys('new search term');
```

#### Submit Form
```php
$searchBox = $driver->findElementByName('q');
$searchBox->sendKeys('search query');
$searchBox->submit();
```

#### Get Element Information
```php
// Get text content
$text = $element->getText();

// Get attribute value
$href = $link->getAttribute('href');
$value = $input->getAttribute('value');
$id = $element->getAttribute('id');

// Get CSS property
$color = $element->getCssValue('color');
$fontSize = $element->getCssValue('font-size');

// Get tag name
$tagName = $element->getTagName(); // e.g., "div", "a", "input"
```

#### Check Element State
```php
// Check if visible
if ($element->isDisplayed()) {
    echo "Element is visible\n";
}

// Check if enabled
if ($button->isEnabled()) {
    echo "Button can be clicked\n";
}

// Check if selected (for checkboxes/radio buttons)
if ($checkbox->isSelected()) {
    echo "Checkbox is checked\n";
}
```

#### Element Location and Size
```php
// Get location (x, y coordinates)
$location = $element->getLocation();
echo "X: {$location['x']}, Y: {$location['y']}\n";

// Get size (width, height)
$size = $element->getSize();
echo "Width: {$size['width']}, Height: {$size['height']}\n";

// Get both (rectangle)
$rect = $element->getRect();
// Returns: ['x' => ..., 'y' => ..., 'width' => ..., 'height' => ...]
```

### Navigation

```php
// Navigate to URL
$driver->get('https://example.com');

// Get current URL
$currentUrl = $driver->getCurrentUrl();

// Get page title
$title = $driver->getTitle();

// Go back
$driver->back();

// Go forward
$driver->forward();

// Refresh page
$driver->refresh();

// Get page source HTML
$html = $driver->getPageSource();
```

### JavaScript Execution

```php
// Execute JavaScript and get return value
$title = $driver->executeScript('return document.title;');

// Execute with arguments
$result = $driver->executeScript(
    'return arguments[0] + arguments[1];',
    [5, 10]
);

// Scroll to element
$element = $driver->findElementById('footer');
$driver->executeScript('arguments[0].scrollIntoView();', [$element->toArray()]);

// Modify page
$driver->executeScript('document.body.style.backgroundColor = "yellow";');

// Execute async JavaScript
$driver->executeAsyncScript('
    var callback = arguments[arguments.length - 1];
    setTimeout(function() {
        callback("done");
    }, 1000);
');
```

### Screenshots

```php
// Take full page screenshot and save
$driver->saveScreenshot('page.png');

// Get screenshot as base64 string
$base64Image = $driver->takeScreenshot();
$imageData = base64_decode($base64Image);

// Take screenshot of specific element
$element = $driver->findElementById('header');
$element->saveScreenshot('header.png');
```

### Window Management

```php
// Maximize window
$driver->maximize();

// Minimize window
$driver->minimize();

// Set custom window size
$driver->setWindowSize(1920, 1080);
$driver->setWindowSize(1280, 720);

// Get window size
$size = $driver->getWindowSize();
echo "Width: {$size['width']}, Height: {$size['height']}\n";
```

### Cookie Management

```php
// Add cookie
$driver->addCookie([
    'name' => 'session_id',
    'value' => 'abc123def456',
    'path' => '/',
    'domain' => '.example.com',
    'secure' => true,
    'httpOnly' => true
]);

// Get all cookies
$cookies = $driver->getCookies();
foreach ($cookies as $cookie) {
    echo "Name: {$cookie['name']}\n";
    echo "Value: {$cookie['value']}\n";
    echo "Domain: {$cookie['domain']}\n";
}

// Delete specific cookie
$driver->deleteCookie('session_id');

// Delete all cookies
$driver->deleteAllCookies();
```

### Waits

#### Wait for Element
```php
// Wait for element to be present (up to 10 seconds)
try {
    $element = $driver->waitForElement('#dynamic-content', 10);
    echo "Element found!\n";
} catch (RuntimeException $e) {
    echo "Element not found after timeout\n";
}

// Wait for element to be visible
$element = $driver->waitForElementVisible('#loading-spinner', 5);
```

#### Wait for Condition
```php
// Wait for custom condition
try {
    $driver->waitUntil(function($driver) {
        return $driver->getTitle() === 'Expected Title';
    }, 10);
    echo "Condition met!\n";
} catch (RuntimeException $e) {
    echo "Condition not met after timeout\n";
}

// Wait for URL to change
$driver->waitUntil(function($driver) {
    return str_contains($driver->getCurrentUrl(), '/success');
}, 15);
```

#### Implicit Wait
```php
// Set implicit wait (applies to all element finding)
$driver->implicitWait(5000); // 5 seconds in milliseconds

// Now all findElement calls will wait up to 5 seconds
$element = $driver->findElementById('dynamic-element');
```

### Finding Child Elements

```php
// Find child elements within a parent element
$parent = $driver->findElementById('parent-container');

// Find child by CSS
$child = $parent->findElementByCssSelector('.child-item');
$children = $parent->findElementsByCssSelector('li');

// Find child by XPath (relative)
$child = $parent->findElementByXPath('.//div[@class="nested"]');

// Find child by tag name
$firstParagraph = $parent->findElementByTagName('p');
$allParagraphs = $parent->findElementsByTagName('p');
```

### Complete Example - Login Flow

```php
<?php

require_once 'vendor/autoload.php';

use Lencls37\PhpSelenium\SeleniumDriver;
use Lencls37\PhpSelenium\WebDriver;

// Setup
$seleniumDriver = new SeleniumDriver();
$seleniumDriver->initialize();

$driver = new WebDriver($seleniumDriver->getDriverPath(), 9515, [
    'goog:chromeOptions' => [
        'binary' => $seleniumDriver->getChromePath(),
        'args' => ['--start-maximized']
    ]
]);
$driver->start();

try {
    // Navigate to login page
    $driver->get('https://example.com/login');
    
    // Wait for page to load
    $driver->waitForElement('#username', 10);
    
    // Fill in login form
    $username = $driver->findElementById('username');
    $username->sendKeys('myuser@example.com');
    
    $password = $driver->findElementById('password');
    $password->sendKeys('mypassword');
    
    // Submit form
    $loginButton = $driver->findElementByCssSelector('button[type="submit"]');
    $loginButton->click();
    
    // Wait for redirect
    $driver->waitUntil(function($driver) {
        return str_contains($driver->getCurrentUrl(), '/dashboard');
    }, 10);
    
    echo "Login successful!\n";
    
    // Take screenshot of dashboard
    $driver->saveScreenshot('dashboard.png');
    
    // Get user info
    $userMenu = $driver->findElementByCssSelector('.user-menu');
    echo "Logged in as: " . $userMenu->getText() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    $driver->saveScreenshot('error.png');
} finally {
    $driver->quit();
}
```

### Complete Example - Web Scraping

```php
<?php

require_once 'vendor/autoload.php';

use Lencls37\PhpSelenium\SeleniumDriver;
use Lencls37\PhpSelenium\WebDriver;

// Setup
$seleniumDriver = new SeleniumDriver();
$seleniumDriver->initialize();

$driver = new WebDriver($seleniumDriver->getDriverPath(), 9515, [
    'goog:chromeOptions' => [
        'binary' => $seleniumDriver->getChromePath(),
        'args' => ['--headless', '--disable-gpu']
    ]
]);
$driver->start();

try {
    // Navigate to page
    $driver->get('https://example.com/products');
    
    // Wait for products to load
    $driver->waitForElement('.product-item', 10);
    
    // Find all products
    $products = $driver->findElementsByCssSelector('.product-item');
    
    echo "Found " . count($products) . " products:\n\n";
    
    foreach ($products as $index => $product) {
        // Get product details
        $name = $product->findElementByCssSelector('.product-name')->getText();
        $price = $product->findElementByCssSelector('.product-price')->getText();
        $link = $product->findElementByCssSelector('a')->getAttribute('href');
        
        echo ($index + 1) . ". $name\n";
        echo "   Price: $price\n";
        echo "   Link: $link\n\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    $driver->quit();
}
```

## Advanced Usage

### Check if Driver Already Exists

```php
$driver = new SeleniumDriver();
$driverPath = $driver->getDriverPath();

if (file_exists($driverPath)) {
    echo "Driver already installed\n";
} else {
    $driver->initialize();
}
```

### Custom Driver Locations

The library automatically detects the project root and stores:
- Drivers in `{project_root}/drivers/`
- Downloaded Chrome in `{project_root}/chrome/`



## Configuration

### Disable SSL Verification

By default, SSL verification is disabled for downloads. This is set in the constructor:

```php
$this->httpClient = new Client([
    'timeout' => 300,
    'verify' => false
]);
```

### Timeout Settings

Download timeout is set to 300 seconds (5 minutes). Modify in constructor if needed.

## Troubleshooting

### Chrome version detected but driver download fails

- Check internet connection
- Ensure firewall allows HTTPS requests
- Try manually downloading from https://chromedriver.chromium.org/downloads

### "Unsupported operating system" error

- Ensure you're running on Windows, Linux, or macOS
- Check PHP_OS constant value

### Permission denied on Linux/macOS

- Ensure the drivers directory is writable
- Check that extracted chromedriver has execute permissions (chmod +x)

### Browser found but version detection fails

- Some Chrome installations may not support --version flag
- The library will try alternative detection methods
- Check browser executable permissions

## Examples

See the following files in the repository:
- `example.php` - Basic usage example
- `demo.php` - Interactive demo
- `test_detection.php` - Browser detection testing

## Support

For issues, please visit: https://github.com/lencls37/php-selenium/issues
