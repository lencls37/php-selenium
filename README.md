# PHP Selenium Driver

Selenium PHP library with automatic Chrome/ChromeDriver setup and multi-browser support.

## Features

- 🚀 Automatic Chrome/Chromium detection and installation
- 📦 Automatic ChromeDriver download and setup
- 🌐 Cross-platform support (Windows, Linux, macOS)
- 🔧 Optional Firefox and Edge support
- 💬 Interactive installation prompts
- ⚡ Version matching between browser and driver

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

### Basic Chrome Setup

```php
<?php

require_once 'vendor/autoload.php';

use Lencls37\PhpSelenium\SeleniumDriver;

// Initialize Chrome driver
$driver = new SeleniumDriver();
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

use Lencls37\PhpSelenium\FirefoxDriver;

$firefoxDriver = new FirefoxDriver();
$firefoxDriver->initialize();

$driverPath = $firefoxDriver->getDriverPath();
$firefoxPath = $firefoxDriver->getBrowserPath();
```

### Edge Support

```php
<?php

use Lencls37\PhpSelenium\EdgeDriver;

$edgeDriver = new EdgeDriver();
$edgeDriver->initialize();

$driverPath = $edgeDriver->getDriverPath();
$edgePath = $edgeDriver->getBrowserPath();
```

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
│   ├── SeleniumDriver.php    # Main Chrome driver class
│   ├── BrowserDriver.php     # Abstract base class
│   ├── FirefoxDriver.php     # Firefox support
│   └── EdgeDriver.php        # Edge support
├── drivers/                  # Downloaded drivers (auto-created)
├── chrome/                   # Downloaded Chrome (auto-created)
├── vendor/                   # Composer dependencies
├── composer.json
├── example.php              # Usage example
└── README.md

```

## Example

Run the included example:

```bash
php example.php
```

This will:
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
    $driver = new SeleniumDriver();
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
