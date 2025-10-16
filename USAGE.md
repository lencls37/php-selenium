# Usage Guide

## Quick Start

### Installation

```bash
composer require lencls37/php-selenium
```

### Basic Usage

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

### Using with php-webdriver

Once setup is complete, use with facebook/php-webdriver:

```php
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;

$driver = new SeleniumDriver();
$driver->initialize();

// Start ChromeDriver process
$process = proc_open(
    [$driver->getDriverPath(), '--port=9515'],
    [
        ['pipe', 'r'],
        ['pipe', 'w'],
        ['pipe', 'w']
    ],
    $pipes
);

// Wait for driver to start
sleep(2);

// Connect to ChromeDriver
$options = new ChromeOptions();
$options->setBinary($driver->getChromePath());

$capabilities = DesiredCapabilities::chrome();
$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

$webDriver = RemoteWebDriver::create(
    'http://localhost:9515',
    $capabilities
);

// Use Selenium
$webDriver->get('https://www.google.com');
echo $webDriver->getTitle(); // "Google"

$webDriver->quit();
proc_close($process);
```

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
