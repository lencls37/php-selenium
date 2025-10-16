# Examples

This directory contains practical examples demonstrating various browser automation capabilities of php-selenium.

## Available Examples

### 1. Form Filling Example
**File:** `form_filling_example.php`

Demonstrates:
- Finding form elements (input, checkbox, button)
- Filling in text fields
- Clicking checkboxes
- Submitting forms
- Checking element state (isSelected)
- Taking screenshots

Run:
```bash
php examples/form_filling_example.php
```

### 2. Web Scraping Example
**File:** `web_scraping_example.php`

Demonstrates:
- Navigating to pages
- Finding multiple elements
- Extracting text and attributes
- Using CSS selectors and XPath
- Executing JavaScript for data extraction
- Saving scraped data to JSON

Run:
```bash
php examples/web_scraping_example.php
```

## Running Examples

All examples require:
1. PHP 8.0 or higher
2. Composer dependencies installed (`composer install`)
3. Chrome/Chromium browser (will be auto-installed if needed)

To run any example:
```bash
cd /path/to/php-selenium
php examples/example_name.php
```

## More Examples

For more examples, check the root directory:
- `quick_start.php` - Simple getting started example
- `browser_control_example.php` - Comprehensive feature demonstration
- `example.php` - Driver setup example
- `demo.php` - Interactive demo

## Creating Your Own

Use these examples as templates for your own automation scripts. The basic pattern is:

```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Lencls37\PhpSelenium\SeleniumDriver;
use Lencls37\PhpSelenium\WebDriver;

// Setup
$seleniumDriver = new SeleniumDriver();
$seleniumDriver->initialize();

$driver = new WebDriver($seleniumDriver->getDriverPath(), 9515, [
    'goog:chromeOptions' => [
        'binary' => $seleniumDriver->getChromePath(),
        'args' => ['--headless']
    ]
]);
$driver->start();

try {
    // Your automation code here
    $driver->get('https://example.com');
    // ... more actions ...
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    $driver->quit();
}
```

## Tips

1. **Headless Mode**: Add `'--headless'` to args for running without GUI
2. **Screenshots**: Use `$driver->saveScreenshot('path.png')` for debugging
3. **Waits**: Use `$driver->waitForElement()` for dynamic content
4. **Error Handling**: Always wrap automation in try-catch and call `$driver->quit()`
5. **Debugging**: Remove `--headless` to see the browser in action

## Common Issues

### Element Not Found
- Use `waitForElement()` instead of `findElement()` for dynamic content
- Verify selector is correct using browser DevTools
- Check if element is in an iframe

### Timeout
- Increase timeout: `$driver->waitForElement($selector, 30)`
- Check network speed
- Verify page actually loads the element

### WebDriver Errors
- Ensure ChromeDriver and Chrome versions match
- Check if port 9515 is available
- Try different port: `new WebDriver($path, 9516)`

## Documentation

For full documentation, see:
- [README.md](../README.md) - Main documentation
- [USAGE.md](../USAGE.md) - Detailed usage guide
