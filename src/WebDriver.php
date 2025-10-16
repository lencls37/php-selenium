<?php

namespace Lencls37\PhpSelenium;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

/**
 * WebDriver class for browser automation
 * Provides Selenium-like browser control functionality
 */
class WebDriver
{
    private Client $httpClient;
    private string $serverUrl;
    private ?string $sessionId = null;
    private $driverProcess = null;
    private array $driverPipes = [];
    private StealthConfig $stealthConfig;

    /**
     * Create a new WebDriver instance
     * 
     * @param string $driverPath Path to the WebDriver executable (chromedriver, geckodriver, etc.)
     * @param int $port Port to run the WebDriver server on
     * @param array $capabilities Browser capabilities
     * @param StealthConfig|null $stealthConfig Stealth configuration (default: enabled)
     */
    public function __construct(
        private string $driverPath,
        private int $port = 9515,
        private array $capabilities = [],
        ?StealthConfig $stealthConfig = null
    ) {
        $this->serverUrl = "http://localhost:{$this->port}";
        $this->httpClient = new Client([
            'timeout' => 30,
            'verify' => false,
            'http_errors' => false
        ]);
        
        // Use default stealth config if none provided (enabled by default)
        $this->stealthConfig = $stealthConfig ?? StealthConfig::default();
    }

    /**
     * Start the WebDriver and create a browser session
     */
    public function start(): void
    {
        // Start the driver process
        $this->startDriver();

        // Wait for driver to be ready
        sleep(2);

        // Create session
        $this->createSession();
        
        // Apply stealth scripts if enabled
        $this->applyStealthMode();
    }

    /**
     * Start the WebDriver process
     */
    private function startDriver(): void
    {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];

        $command = escapeshellarg($this->driverPath) . " --port={$this->port}";
        
        $this->driverProcess = proc_open(
            $command,
            $descriptors,
            $this->driverPipes
        );

        if (!is_resource($this->driverProcess)) {
            throw new RuntimeException("Failed to start WebDriver at: {$this->driverPath}");
        }
    }

    /**
     * Create a browser session
     */
    private function createSession(): void
    {
        // Prepare base capabilities
        $baseCaps = [
            'browserName' => 'chrome',
            'goog:chromeOptions' => [
                'args' => []
            ]
        ];
        
        // Merge with user-provided capabilities
        $caps = array_merge($baseCaps, $this->capabilities);
        
        // Apply stealth options to Chrome options
        if (isset($caps['goog:chromeOptions']) && $this->stealthConfig->isEnabled()) {
            $caps['goog:chromeOptions'] = $this->stealthConfig->mergeWithChromeOptions(
                $caps['goog:chromeOptions']
            );
        }
        
        $response = $this->request('POST', '/session', [
            'capabilities' => [
                'alwaysMatch' => $caps
            ]
        ]);

        if (!isset($response['value']['sessionId'])) {
            throw new RuntimeException("Failed to create WebDriver session");
        }

        $this->sessionId = $response['value']['sessionId'];
    }
    
    /**
     * Apply stealth mode JavaScript to hide Selenium detection
     */
    private function applyStealthMode(): void
    {
        if (!$this->stealthConfig->isEnabled()) {
            return;
        }
        
        $script = $this->stealthConfig->getStealthScript();
        if (!empty($script)) {
            try {
                $this->executeScript($script);
            } catch (\Exception $e) {
                // Silently fail if stealth script injection fails
                // to not break the session
            }
        }
    }

    /**
     * Navigate to a URL
     * 
     * @param string $url The URL to navigate to
     */
    public function get(string $url): void
    {
        $this->request('POST', "/session/{$this->sessionId}/url", [
            'url' => $url
        ]);
        
        // Re-apply stealth mode after navigation
        $this->applyStealthMode();
    }

    /**
     * Get the current page URL
     * 
     * @return string Current URL
     */
    public function getCurrentUrl(): string
    {
        $response = $this->request('GET', "/session/{$this->sessionId}/url");
        return $response['value'] ?? '';
    }

    /**
     * Get the page title
     * 
     * @return string Page title
     */
    public function getTitle(): string
    {
        $response = $this->request('GET', "/session/{$this->sessionId}/title");
        return $response['value'] ?? '';
    }

    /**
     * Get the page source
     * 
     * @return string HTML source
     */
    public function getPageSource(): string
    {
        $response = $this->request('GET', "/session/{$this->sessionId}/source");
        return $response['value'] ?? '';
    }

    /**
     * Find an element by CSS selector
     * 
     * @param string $selector CSS selector
     * @return WebElement
     */
    public function findElementByCssSelector(string $selector): WebElement
    {
        return $this->findElement('css selector', $selector);
    }

    /**
     * Find elements by CSS selector
     * 
     * @param string $selector CSS selector
     * @return WebElement[]
     */
    public function findElementsByCssSelector(string $selector): array
    {
        return $this->findElements('css selector', $selector);
    }

    /**
     * Find an element by XPath
     * 
     * @param string $xpath XPath expression
     * @return WebElement
     */
    public function findElementByXPath(string $xpath): WebElement
    {
        return $this->findElement('xpath', $xpath);
    }

    /**
     * Find elements by XPath
     * 
     * @param string $xpath XPath expression
     * @return WebElement[]
     */
    public function findElementsByXPath(string $xpath): array
    {
        return $this->findElements('xpath', $xpath);
    }

    /**
     * Find an element by ID
     * 
     * @param string $id Element ID
     * @return WebElement
     */
    public function findElementById(string $id): WebElement
    {
        return $this->findElementByCssSelector("#" . $id);
    }

    /**
     * Find an element by name attribute
     * 
     * @param string $name Name attribute value
     * @return WebElement
     */
    public function findElementByName(string $name): WebElement
    {
        return $this->findElement('css selector', "[name='{$name}']");
    }

    /**
     * Find elements by name attribute
     * 
     * @param string $name Name attribute value
     * @return WebElement[]
     */
    public function findElementsByName(string $name): array
    {
        return $this->findElements('css selector', "[name='{$name}']");
    }

    /**
     * Find an element by class name
     * 
     * @param string $className Class name
     * @return WebElement
     */
    public function findElementByClassName(string $className): WebElement
    {
        return $this->findElementByCssSelector("." . $className);
    }

    /**
     * Find elements by class name
     * 
     * @param string $className Class name
     * @return WebElement[]
     */
    public function findElementsByClassName(string $className): array
    {
        return $this->findElementsByCssSelector("." . $className);
    }

    /**
     * Find an element by tag name
     * 
     * @param string $tagName Tag name
     * @return WebElement
     */
    public function findElementByTagName(string $tagName): WebElement
    {
        return $this->findElement('css selector', $tagName);
    }

    /**
     * Find elements by tag name
     * 
     * @param string $tagName Tag name
     * @return WebElement[]
     */
    public function findElementsByTagName(string $tagName): array
    {
        return $this->findElements('css selector', $tagName);
    }

    /**
     * Find an element by link text
     * 
     * @param string $linkText Link text
     * @return WebElement
     */
    public function findElementByLinkText(string $linkText): WebElement
    {
        return $this->findElement('link text', $linkText);
    }

    /**
     * Find an element by partial link text
     * 
     * @param string $linkText Partial link text
     * @return WebElement
     */
    public function findElementByPartialLinkText(string $linkText): WebElement
    {
        return $this->findElement('partial link text', $linkText);
    }

    /**
     * Generic find element method
     * 
     * @param string $using Strategy (css selector, xpath, etc.)
     * @param string $value Selector value
     * @return WebElement
     */
    private function findElement(string $using, string $value): WebElement
    {
        $response = $this->request('POST', "/session/{$this->sessionId}/element", [
            'using' => $using,
            'value' => $value
        ]);

        if (!isset($response['value'])) {
            throw new RuntimeException("Element not found: {$using} = {$value}");
        }

        $elementId = $this->extractElementId($response['value']);
        return new WebElement($this, $elementId);
    }

    /**
     * Generic find elements method
     * 
     * @param string $using Strategy (css selector, xpath, etc.)
     * @param string $value Selector value
     * @return WebElement[]
     */
    private function findElements(string $using, string $value): array
    {
        $response = $this->request('POST', "/session/{$this->sessionId}/elements", [
            'using' => $using,
            'value' => $value
        ]);

        $elements = [];
        if (isset($response['value']) && is_array($response['value'])) {
            foreach ($response['value'] as $elementData) {
                $elementId = $this->extractElementId($elementData);
                $elements[] = new WebElement($this, $elementId);
            }
        }

        return $elements;
    }

    /**
     * Extract element ID from response
     */
    private function extractElementId(array $data): string
    {
        // W3C format
        if (isset($data['element-6066-11e4-a52e-4f735466cecf'])) {
            return $data['element-6066-11e4-a52e-4f735466cecf'];
        }
        // Legacy format
        if (isset($data['ELEMENT'])) {
            return $data['ELEMENT'];
        }
        throw new RuntimeException("Could not extract element ID from response");
    }

    /**
     * Execute JavaScript
     * 
     * @param string $script JavaScript code to execute
     * @param array $args Arguments to pass to the script
     * @return mixed Script return value
     */
    public function executeScript(string $script, array $args = []): mixed
    {
        $response = $this->request('POST', "/session/{$this->sessionId}/execute/sync", [
            'script' => $script,
            'args' => $args
        ]);

        return $response['value'] ?? null;
    }

    /**
     * Execute asynchronous JavaScript
     * 
     * @param string $script JavaScript code to execute
     * @param array $args Arguments to pass to the script
     * @return mixed Script return value
     */
    public function executeAsyncScript(string $script, array $args = []): mixed
    {
        $response = $this->request('POST', "/session/{$this->sessionId}/execute/async", [
            'script' => $script,
            'args' => $args
        ]);

        return $response['value'] ?? null;
    }

    /**
     * Take a screenshot
     * 
     * @return string Base64 encoded PNG image
     */
    public function takeScreenshot(): string
    {
        $response = $this->request('GET', "/session/{$this->sessionId}/screenshot");
        return $response['value'] ?? '';
    }

    /**
     * Save a screenshot to file
     * 
     * @param string $filename Path to save the screenshot
     */
    public function saveScreenshot(string $filename): void
    {
        $base64 = $this->takeScreenshot();
        $imageData = base64_decode($base64);
        file_put_contents($filename, $imageData);
    }

    /**
     * Navigate back
     */
    public function back(): void
    {
        $this->request('POST', "/session/{$this->sessionId}/back");
        // Re-apply stealth mode after navigation
        $this->applyStealthMode();
    }

    /**
     * Navigate forward
     */
    public function forward(): void
    {
        $this->request('POST', "/session/{$this->sessionId}/forward");
        // Re-apply stealth mode after navigation
        $this->applyStealthMode();
    }

    /**
     * Refresh the page
     */
    public function refresh(): void
    {
        $this->request('POST', "/session/{$this->sessionId}/refresh");
        // Re-apply stealth mode after page refresh
        $this->applyStealthMode();
    }

    /**
     * Maximize the window
     */
    public function maximize(): void
    {
        $this->request('POST', "/session/{$this->sessionId}/window/maximize");
    }

    /**
     * Minimize the window
     */
    public function minimize(): void
    {
        $this->request('POST', "/session/{$this->sessionId}/window/minimize");
    }

    /**
     * Set window size
     * 
     * @param int $width Window width
     * @param int $height Window height
     */
    public function setWindowSize(int $width, int $height): void
    {
        $this->request('POST', "/session/{$this->sessionId}/window/rect", [
            'width' => $width,
            'height' => $height
        ]);
    }

    /**
     * Get window size
     * 
     * @return array ['width' => int, 'height' => int]
     */
    public function getWindowSize(): array
    {
        $response = $this->request('GET', "/session/{$this->sessionId}/window/rect");
        return [
            'width' => $response['value']['width'] ?? 0,
            'height' => $response['value']['height'] ?? 0
        ];
    }

    /**
     * Add a cookie
     * 
     * @param array $cookie Cookie data
     */
    public function addCookie(array $cookie): void
    {
        $this->request('POST', "/session/{$this->sessionId}/cookie", [
            'cookie' => $cookie
        ]);
    }

    /**
     * Get all cookies
     * 
     * @return array Array of cookies
     */
    public function getCookies(): array
    {
        $response = $this->request('GET', "/session/{$this->sessionId}/cookie");
        return $response['value'] ?? [];
    }

    /**
     * Delete all cookies
     */
    public function deleteAllCookies(): void
    {
        $this->request('DELETE', "/session/{$this->sessionId}/cookie");
    }

    /**
     * Delete a specific cookie
     * 
     * @param string $name Cookie name
     */
    public function deleteCookie(string $name): void
    {
        $this->request('DELETE', "/session/{$this->sessionId}/cookie/{$name}");
    }

    /**
     * Wait for an element to be present
     * 
     * @param string $cssSelector CSS selector
     * @param int $timeoutSeconds Timeout in seconds
     * @param int $pollIntervalMs Poll interval in milliseconds
     * @return WebElement
     */
    public function waitForElement(string $cssSelector, int $timeoutSeconds = 10, int $pollIntervalMs = 500): WebElement
    {
        $endTime = microtime(true) + $timeoutSeconds;
        
        while (microtime(true) < $endTime) {
            try {
                return $this->findElementByCssSelector($cssSelector);
            } catch (RuntimeException $e) {
                usleep($pollIntervalMs * 1000);
            }
        }

        throw new RuntimeException("Element not found after {$timeoutSeconds} seconds: {$cssSelector}");
    }

    /**
     * Wait for an element to be visible
     * 
     * @param string $cssSelector CSS selector
     * @param int $timeoutSeconds Timeout in seconds
     * @param int $pollIntervalMs Poll interval in milliseconds
     * @return WebElement
     */
    public function waitForElementVisible(string $cssSelector, int $timeoutSeconds = 10, int $pollIntervalMs = 500): WebElement
    {
        $endTime = microtime(true) + $timeoutSeconds;
        
        while (microtime(true) < $endTime) {
            try {
                $element = $this->findElementByCssSelector($cssSelector);
                if ($element->isDisplayed()) {
                    return $element;
                }
            } catch (RuntimeException $e) {
                // Element not found, continue waiting
            }
            usleep($pollIntervalMs * 1000);
        }

        throw new RuntimeException("Element not visible after {$timeoutSeconds} seconds: {$cssSelector}");
    }

    /**
     * Wait for a condition to be true
     * 
     * @param callable $condition Condition function that returns bool
     * @param int $timeoutSeconds Timeout in seconds
     * @param int $pollIntervalMs Poll interval in milliseconds
     * @return bool
     */
    public function waitUntil(callable $condition, int $timeoutSeconds = 10, int $pollIntervalMs = 500): bool
    {
        $endTime = microtime(true) + $timeoutSeconds;
        
        while (microtime(true) < $endTime) {
            if ($condition($this)) {
                return true;
            }
            usleep($pollIntervalMs * 1000);
        }

        throw new RuntimeException("Condition not met after {$timeoutSeconds} seconds");
    }

    /**
     * Implicit wait - set timeout for finding elements
     * 
     * @param int $timeoutMs Timeout in milliseconds
     */
    public function implicitWait(int $timeoutMs): void
    {
        $this->request('POST', "/session/{$this->sessionId}/timeouts", [
            'implicit' => $timeoutMs
        ]);
    }

    /**
     * Make HTTP request to WebDriver
     * 
     * @param string $method HTTP method
     * @param string $path Request path
     * @param array $data Request data
     * @return array Response data
     */
    public function request(string $method, string $path, array $data = []): array
    {
        try {
            $options = [];
            if (!empty($data)) {
                $options['json'] = $data;
            }

            $response = $this->httpClient->request(
                $method,
                $this->serverUrl . $path,
                $options
            );

            $body = $response->getBody()->getContents();
            return json_decode($body, true) ?? [];
        } catch (GuzzleException $e) {
            throw new RuntimeException("WebDriver request failed: " . $e->getMessage());
        }
    }

    /**
     * Get session ID
     */
    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }
    
    /**
     * Get stealth configuration
     */
    public function getStealthConfig(): StealthConfig
    {
        return $this->stealthConfig;
    }

    /**
     * Close the current window
     */
    public function close(): void
    {
        if ($this->sessionId) {
            $this->request('DELETE', "/session/{$this->sessionId}/window");
        }
    }

    /**
     * Quit the browser and stop the driver
     */
    public function quit(): void
    {
        // Delete session
        if ($this->sessionId) {
            try {
                $this->request('DELETE', "/session/{$this->sessionId}");
            } catch (RuntimeException $e) {
                // Ignore errors on quit
            }
            $this->sessionId = null;
        }

        // Close pipes
        foreach ($this->driverPipes as $pipe) {
            if (is_resource($pipe)) {
                fclose($pipe);
            }
        }

        // Terminate driver process
        if (is_resource($this->driverProcess)) {
            proc_terminate($this->driverProcess);
            proc_close($this->driverProcess);
            $this->driverProcess = null;
        }
    }

    /**
     * Destructor - ensure driver is stopped
     */
    public function __destruct()
    {
        $this->quit();
    }
}
