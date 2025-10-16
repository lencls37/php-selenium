# WebDriver Protocol Implementation

This document lists all the WebDriver protocol endpoints implemented in this library, following the W3C WebDriver specification.

## 🧭 Session Management

| Method | Endpoint | Description | Implementation |
|--------|----------|-------------|----------------|
| POST | `/session` | Create a new browser session | `WebDriver->start()` |
| GET | `/sessions` | List all active sessions | `WebDriver->getSessions()` |
| DELETE | `/session/{sessionId}` | Close a session (quit browser) | `WebDriver->quit()` |
| GET | `/status` | Check if WebDriver is operational | `WebDriver->getStatus()` |

## 🌐 Navigation

| Method | Endpoint | Description | Implementation |
|--------|----------|-------------|----------------|
| POST | `/session/{sessionId}/url` | Navigate to URL | `WebDriver->get($url)` |
| GET | `/session/{sessionId}/url` | Get current page URL | `WebDriver->getCurrentUrl()` |
| POST | `/session/{sessionId}/back` | Navigate back | `WebDriver->back()` |
| POST | `/session/{sessionId}/forward` | Navigate forward | `WebDriver->forward()` |
| POST | `/session/{sessionId}/refresh` | Refresh page | `WebDriver->refresh()` |

## 📄 Page Information

| Method | Endpoint | Description | Implementation |
|--------|----------|-------------|----------------|
| GET | `/session/{sessionId}/title` | Get page title | `WebDriver->getTitle()` |
| GET | `/session/{sessionId}/source` | Get page HTML source | `WebDriver->getPageSource()` or `getHtml()` |

## 🔍 Element Finding

| Method | Endpoint | Description | Implementation |
|--------|----------|-------------|----------------|
| POST | `/session/{sessionId}/element` | Find first matching element | `WebDriver->findElement*()` methods |
| POST | `/session/{sessionId}/elements` | Find all matching elements | `WebDriver->findElements*()` methods |
| POST | `/session/{sessionId}/element/{elementId}/element` | Find child element | `WebElement->findElement*()` methods |
| POST | `/session/{sessionId}/element/{elementId}/elements` | Find child elements | `WebElement->findElements*()` methods |

**Supported strategies:**
- `css selector` - `findElementByCssSelector()`
- `xpath` - `findElementByXPath()`
- `id` - `findElementById()`
- `name` - `findElementByName()`
- `link text` - `findElementByLinkText()`
- `partial link text` - `findElementByPartialLinkText()`
- `tag name` - `findElementByTagName()`
- `class name` - `findElementByClassName()`

## 🧱 Element Interaction

| Method | Endpoint | Description | Implementation |
|--------|----------|-------------|----------------|
| POST | `/session/{sessionId}/element/{elementId}/click` | Click element | `WebElement->click()` |
| POST | `/session/{sessionId}/element/{elementId}/clear` | Clear input field | `WebElement->clear()` |
| POST | `/session/{sessionId}/element/{elementId}/value` | Send keys to element | `WebElement->sendKeys($text)` |
| GET | `/session/{sessionId}/element/{elementId}/text` | Get element text | `WebElement->getText()` |
| GET | `/session/{sessionId}/element/{elementId}/attribute/{name}` | Get HTML attribute | `WebElement->getAttribute($name)` |
| GET | `/session/{sessionId}/element/{elementId}/property/{name}` | Get DOM property | `WebElement->getProperty($name)` |
| GET | `/session/{sessionId}/element/{elementId}/css/{propertyName}` | Get CSS value | `WebElement->getCssValue($name)` |
| GET | `/session/{sessionId}/element/{elementId}/displayed` | Check if visible | `WebElement->isDisplayed()` |
| GET | `/session/{sessionId}/element/{elementId}/enabled` | Check if enabled | `WebElement->isEnabled()` |
| GET | `/session/{sessionId}/element/{elementId}/selected` | Check if selected | `WebElement->isSelected()` |
| GET | `/session/{sessionId}/element/{elementId}/rect` | Get element position and size | `WebElement->getRect()`, `getLocation()`, `getSize()` |

## 📦 Frame and Window Management

| Method | Endpoint | Description | Implementation |
|--------|----------|-------------|----------------|
| GET | `/session/{sessionId}/window` | Get current window handle | `WebDriver->getWindowHandle()` |
| DELETE | `/session/{sessionId}/window` | Close current window | `WebDriver->close()` |
| GET | `/session/{sessionId}/window/handles` | Get all window handles | `WebDriver->getWindowHandles()` |
| POST | `/session/{sessionId}/window` | Switch to window | `WebDriver->switchToWindow($handle)` |
| POST | `/session/{sessionId}/frame` | Switch to frame | `WebDriver->switchToFrame($frame)` |
| POST | `/session/{sessionId}/frame/parent` | Switch to parent frame | `WebDriver->switchToParentFrame()` |
| GET | `/session/{sessionId}/window/rect` | Get window size and position | `WebDriver->getWindowRect()` |
| POST | `/session/{sessionId}/window/rect` | Set window size and position | `WebDriver->setWindowRect($x, $y, $width, $height)` |
| POST | `/session/{sessionId}/window/maximize` | Maximize window | `WebDriver->maximize()` |
| POST | `/session/{sessionId}/window/minimize` | Minimize window | `WebDriver->minimize()` |

## ⌨️ Keyboard and Mouse Actions

| Method | Endpoint | Description | Implementation |
|--------|----------|-------------|----------------|
| POST | `/session/{sessionId}/actions` | Perform keyboard/mouse actions | `WebDriver->performActions($actions)` |
| DELETE | `/session/{sessionId}/actions` | Release all actions | `WebDriver->releaseActions()` |

## 🖼️ Screenshots

| Method | Endpoint | Description | Implementation |
|--------|----------|-------------|----------------|
| GET | `/session/{sessionId}/screenshot` | Take page screenshot | `WebDriver->takeScreenshot()`, `saveScreenshot($file)` |
| GET | `/session/{sessionId}/element/{elementId}/screenshot` | Take element screenshot | `WebElement->takeScreenshot()`, `saveScreenshot($file)` |

## 🍪 Cookie Management

| Method | Endpoint | Description | Implementation |
|--------|----------|-------------|----------------|
| GET | `/session/{sessionId}/cookie` | Get all cookies | `WebDriver->getCookies()` |
| POST | `/session/{sessionId}/cookie` | Add cookie | `WebDriver->addCookie($cookie)` |
| GET | `/session/{sessionId}/cookie/{name}` | Get specific cookie | `WebDriver->getCookie($name)` |
| DELETE | `/session/{sessionId}/cookie/{name}` | Delete specific cookie | `WebDriver->deleteCookie($name)` |
| DELETE | `/session/{sessionId}/cookie` | Delete all cookies | `WebDriver->deleteAllCookies()` |

## 💻 Script Execution

| Method | Endpoint | Description | Implementation |
|--------|----------|-------------|----------------|
| POST | `/session/{sessionId}/execute/sync` | Execute synchronous JavaScript | `WebDriver->executeScript($script, $args)` |
| POST | `/session/{sessionId}/execute/async` | Execute asynchronous JavaScript | `WebDriver->executeAsyncScript($script, $args)` |

## 📑 Alert Management

| Method | Endpoint | Description | Implementation |
|--------|----------|-------------|----------------|
| GET | `/session/{sessionId}/alert/text` | Get alert text | `WebDriver->getAlertText()` |
| POST | `/session/{sessionId}/alert/accept` | Accept alert (OK) | `WebDriver->acceptAlert()` |
| POST | `/session/{sessionId}/alert/dismiss` | Dismiss alert (Cancel) | `WebDriver->dismissAlert()` |
| POST | `/session/{sessionId}/alert/text` | Send text to alert | `WebDriver->sendAlertText($text)` |

## 📋 Timeouts

| Method | Endpoint | Description | Implementation |
|--------|----------|-------------|----------------|
| GET | `/session/{sessionId}/timeouts` | Get timeout values | `WebDriver->getTimeouts()` |
| POST | `/session/{sessionId}/timeouts` | Set timeout values | `WebDriver->setTimeouts($implicit, $pageLoad, $script)` or `implicitWait($ms)` |

## 🔒 Capabilities

| Method | Endpoint | Description | Implementation |
|--------|----------|-------------|----------------|
| GET | `/session/{sessionId}` | Get session capabilities | `WebDriver->getCapabilities()` |

## Additional Features

### Wait Methods (Higher-level helpers)
- `waitForElement($selector, $timeout)` - Wait for element to be present
- `waitForElementVisible($selector, $timeout)` - Wait for element to be visible
- `waitUntil($condition, $timeout)` - Wait for custom condition
- `waitForPageLoad($timeout)` - Wait for page to fully load
- `waitForPageReady($timeout)` - Wait for DOM to be ready
- `waitForAjax($timeout)` - Wait for jQuery AJAX requests

### Stealth Mode
The library includes built-in stealth mode (enabled by default) to help bypass bot detection:
- Hides `navigator.webdriver` property
- Removes Chrome automation flags
- Disables "Chrome is being controlled" infobar
- Adds natural-looking plugins and languages
- Custom user agent support

See `StealthConfig` class for configuration options.

## Usage Examples

### Session Management
```php
// Create session and start browser
$driver = new WebDriver($driverPath, 9515);
$driver->start();

// Get status
$status = $driver->getStatus();

// Quit session
$driver->quit();
```

### Window Management
```php
// Get current window
$handle = $driver->getWindowHandle();

// Get all windows
$handles = $driver->getWindowHandles();

// Switch to another window
$driver->switchToWindow($handles[1]);

// Resize and position window
$driver->setWindowRect(0, 0, 1920, 1080);
```

### Frame Management
```php
// Switch to frame by index
$driver->switchToFrame(0);

// Switch to frame by element
$frameElement = $driver->findElementByTagName('iframe');
$driver->switchToFrame($frameElement);

// Switch back to default content
$driver->switchToFrame(null);

// Switch to parent frame
$driver->switchToParentFrame();
```

### Alert Handling
```php
// Get alert text
$text = $driver->getAlertText();

// Accept alert
$driver->acceptAlert();

// Dismiss alert
$driver->dismissAlert();

// Send text to prompt
$driver->sendAlertText("My input");
$driver->acceptAlert();
```

### Element Properties
```php
$element = $driver->findElementById('myInput');

// Get attribute (from HTML)
$placeholder = $element->getAttribute('placeholder');

// Get property (from DOM)
$value = $element->getProperty('value');
$checked = $element->getProperty('checked');
```

### Timeouts
```php
// Set all timeouts at once
$driver->setTimeouts(
    implicitMs: 5000,    // 5 seconds for element finding
    pageLoadMs: 30000,   // 30 seconds for page load
    scriptMs: 10000      // 10 seconds for script execution
);

// Get current timeouts
$timeouts = $driver->getTimeouts();
```

### Capabilities
```php
// Get session capabilities
$capabilities = $driver->getCapabilities();
print_r($capabilities);
```

## Browser Drivers

### Chrome Driver
```php
use Lencls37\PhpSelenium\ChromeDriver;

$chromeDriver = new ChromeDriver();
$chromeDriver->initialize();
$driverPath = $chromeDriver->getDriverPath();
```

### Gecko Driver (Firefox)
```php
use Lencls37\PhpSelenium\GeckoDriver;

$geckoDriver = new GeckoDriver();
$geckoDriver->initialize();
$driverPath = $geckoDriver->getDriverPath();
```

## Reference

This implementation follows the [W3C WebDriver Specification](https://www.w3.org/TR/webdriver/).
