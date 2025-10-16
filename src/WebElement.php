<?php

namespace Lencls37\PhpSelenium;

use RuntimeException;

/**
 * WebElement class representing a DOM element
 * Provides methods for interacting with elements
 */
class WebElement
{
    /**
     * Create a new WebElement
     * 
     * @param WebDriver $driver WebDriver instance
     * @param string $elementId Element ID from WebDriver
     */
    public function __construct(
        private WebDriver $driver,
        private string $elementId
    ) {
    }

    /**
     * Click the element
     */
    public function click(): void
    {
        $sessionId = $this->driver->getSessionId();
        $this->driver->request('POST', "/session/{$sessionId}/element/{$this->elementId}/click");
    }

    /**
     * Send keys to the element (type text)
     * 
     * @param string $text Text to type
     */
    public function sendKeys(string $text): void
    {
        $sessionId = $this->driver->getSessionId();
        $this->driver->request('POST', "/session/{$sessionId}/element/{$this->elementId}/value", [
            'text' => $text,
            'value' => str_split($text)
        ]);
    }

    /**
     * Clear the element's value (for input fields)
     */
    public function clear(): void
    {
        $sessionId = $this->driver->getSessionId();
        $this->driver->request('POST', "/session/{$sessionId}/element/{$this->elementId}/clear");
    }

    /**
     * Get element text content
     * 
     * @return string Element text
     */
    public function getText(): string
    {
        $sessionId = $this->driver->getSessionId();
        $response = $this->driver->request('GET', "/session/{$sessionId}/element/{$this->elementId}/text");
        return $response['value'] ?? '';
    }

    /**
     * Get an attribute value
     * 
     * @param string $name Attribute name
     * @return string|null Attribute value
     */
    public function getAttribute(string $name): ?string
    {
        $sessionId = $this->driver->getSessionId();
        $response = $this->driver->request('GET', "/session/{$sessionId}/element/{$this->elementId}/attribute/{$name}");
        return $response['value'] ?? null;
    }

    /**
     * Get a CSS property value
     * 
     * @param string $name CSS property name
     * @return string CSS property value
     */
    public function getCssValue(string $name): string
    {
        $sessionId = $this->driver->getSessionId();
        $response = $this->driver->request('GET', "/session/{$sessionId}/element/{$this->elementId}/css/{$name}");
        return $response['value'] ?? '';
    }

    /**
     * Get element tag name
     * 
     * @return string Tag name
     */
    public function getTagName(): string
    {
        $sessionId = $this->driver->getSessionId();
        $response = $this->driver->request('GET', "/session/{$sessionId}/element/{$this->elementId}/name");
        return $response['value'] ?? '';
    }

    /**
     * Check if element is displayed
     * 
     * @return bool True if displayed
     */
    public function isDisplayed(): bool
    {
        $sessionId = $this->driver->getSessionId();
        $response = $this->driver->request('GET', "/session/{$sessionId}/element/{$this->elementId}/displayed");
        return $response['value'] ?? false;
    }

    /**
     * Check if element is enabled
     * 
     * @return bool True if enabled
     */
    public function isEnabled(): bool
    {
        $sessionId = $this->driver->getSessionId();
        $response = $this->driver->request('GET', "/session/{$sessionId}/element/{$this->elementId}/enabled");
        return $response['value'] ?? false;
    }

    /**
     * Check if element is selected (for checkboxes/radio buttons)
     * 
     * @return bool True if selected
     */
    public function isSelected(): bool
    {
        $sessionId = $this->driver->getSessionId();
        $response = $this->driver->request('GET', "/session/{$sessionId}/element/{$this->elementId}/selected");
        return $response['value'] ?? false;
    }

    /**
     * Submit a form
     */
    public function submit(): void
    {
        // Submit by executing JavaScript
        $script = "arguments[0].form.submit();";
        $this->driver->executeScript($script, [$this->toArray()]);
    }

    /**
     * Get element location (x, y coordinates)
     * 
     * @return array ['x' => int, 'y' => int]
     */
    public function getLocation(): array
    {
        $sessionId = $this->driver->getSessionId();
        $response = $this->driver->request('GET', "/session/{$sessionId}/element/{$this->elementId}/rect");
        return [
            'x' => $response['value']['x'] ?? 0,
            'y' => $response['value']['y'] ?? 0
        ];
    }

    /**
     * Get element size (width, height)
     * 
     * @return array ['width' => int, 'height' => int]
     */
    public function getSize(): array
    {
        $sessionId = $this->driver->getSessionId();
        $response = $this->driver->request('GET', "/session/{$sessionId}/element/{$this->elementId}/rect");
        return [
            'width' => $response['value']['width'] ?? 0,
            'height' => $response['value']['height'] ?? 0
        ];
    }

    /**
     * Get element rectangle (location and size)
     * 
     * @return array ['x' => int, 'y' => int, 'width' => int, 'height' => int]
     */
    public function getRect(): array
    {
        $sessionId = $this->driver->getSessionId();
        $response = $this->driver->request('GET', "/session/{$sessionId}/element/{$this->elementId}/rect");
        return [
            'x' => $response['value']['x'] ?? 0,
            'y' => $response['value']['y'] ?? 0,
            'width' => $response['value']['width'] ?? 0,
            'height' => $response['value']['height'] ?? 0
        ];
    }

    /**
     * Take a screenshot of this element
     * 
     * @return string Base64 encoded PNG image
     */
    public function takeScreenshot(): string
    {
        $sessionId = $this->driver->getSessionId();
        $response = $this->driver->request('GET', "/session/{$sessionId}/element/{$this->elementId}/screenshot");
        return $response['value'] ?? '';
    }

    /**
     * Save element screenshot to file
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
     * Find a child element by CSS selector
     * 
     * @param string $selector CSS selector
     * @return WebElement
     */
    public function findElementByCssSelector(string $selector): WebElement
    {
        return $this->findElement('css selector', $selector);
    }

    /**
     * Find child elements by CSS selector
     * 
     * @param string $selector CSS selector
     * @return WebElement[]
     */
    public function findElementsByCssSelector(string $selector): array
    {
        return $this->findElements('css selector', $selector);
    }

    /**
     * Find a child element by XPath
     * 
     * @param string $xpath XPath expression
     * @return WebElement
     */
    public function findElementByXPath(string $xpath): WebElement
    {
        return $this->findElement('xpath', $xpath);
    }

    /**
     * Find child elements by XPath
     * 
     * @param string $xpath XPath expression
     * @return WebElement[]
     */
    public function findElementsByXPath(string $xpath): array
    {
        return $this->findElements('xpath', $xpath);
    }

    /**
     * Find a child element by tag name
     * 
     * @param string $tagName Tag name
     * @return WebElement
     */
    public function findElementByTagName(string $tagName): WebElement
    {
        return $this->findElement('css selector', $tagName);
    }

    /**
     * Find child elements by tag name
     * 
     * @param string $tagName Tag name
     * @return WebElement[]
     */
    public function findElementsByTagName(string $tagName): array
    {
        return $this->findElements('css selector', $tagName);
    }

    /**
     * Generic find child element method
     * 
     * @param string $using Strategy
     * @param string $value Selector value
     * @return WebElement
     */
    private function findElement(string $using, string $value): WebElement
    {
        $sessionId = $this->driver->getSessionId();
        $response = $this->driver->request('POST', "/session/{$sessionId}/element/{$this->elementId}/element", [
            'using' => $using,
            'value' => $value
        ]);

        if (!isset($response['value'])) {
            throw new RuntimeException("Element not found: {$using} = {$value}");
        }

        $elementId = $this->extractElementId($response['value']);
        return new WebElement($this->driver, $elementId);
    }

    /**
     * Generic find child elements method
     * 
     * @param string $using Strategy
     * @param string $value Selector value
     * @return WebElement[]
     */
    private function findElements(string $using, string $value): array
    {
        $sessionId = $this->driver->getSessionId();
        $response = $this->driver->request('POST', "/session/{$sessionId}/element/{$this->elementId}/elements", [
            'using' => $using,
            'value' => $value
        ]);

        $elements = [];
        if (isset($response['value']) && is_array($response['value'])) {
            foreach ($response['value'] as $elementData) {
                $elementId = $this->extractElementId($elementData);
                $elements[] = new WebElement($this->driver, $elementId);
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
     * Convert element to array format for JavaScript execution
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'element-6066-11e4-a52e-4f735466cecf' => $this->elementId,
            'ELEMENT' => $this->elementId
        ];
    }

    /**
     * Get element ID
     * 
     * @return string
     */
    public function getId(): string
    {
        return $this->elementId;
    }
}
