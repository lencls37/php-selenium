<?php

namespace Lencls37\PhpSelenium;

/**
 * Stealth configuration for hiding Selenium from bot detection
 * 
 * Provides various options to bypass bot protection systems by:
 * - Hiding navigator.webdriver property
 * - Modifying Chrome DevTools Protocol signatures
 * - Customizing user agent
 * - Disabling automation flags
 */
class StealthConfig
{
    /**
     * Create a new stealth configuration
     * 
     * @param bool $enabled Enable stealth mode (default: true)
     * @param bool $hideWebdriver Hide navigator.webdriver property (default: true)
     * @param bool $hideAutomation Hide Chrome automation extension (default: true)
     * @param string|null $userAgent Custom user agent (null for default)
     * @param bool $disableInfobars Disable "Chrome is being controlled" infobar (default: true)
     * @param bool $excludeSwitches Exclude automation switches (default: true)
     * @param bool $modifyPermissions Modify permission requests to appear more natural (default: true)
     */
    public function __construct(
        private bool $enabled = true,
        private bool $hideWebdriver = true,
        private bool $hideAutomation = true,
        private ?string $userAgent = null,
        private bool $disableInfobars = true,
        private bool $excludeSwitches = true,
        private bool $modifyPermissions = true
    ) {
    }

    /**
     * Check if stealth mode is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Get Chrome options for stealth mode
     */
    public function getChromeOptions(): array
    {
        if (!$this->enabled) {
            return [];
        }

        $options = [
            'args' => [],
            'excludeSwitches' => [],
            'prefs' => []
        ];

        // Disable automation flags
        if ($this->hideAutomation) {
            $options['excludeSwitches'][] = 'enable-automation';
            $options['args'][] = '--disable-blink-features=AutomationControlled';
        }

        // Disable infobars
        if ($this->disableInfobars) {
            $options['args'][] = '--disable-infobars';
        }

        // Custom user agent
        if ($this->userAgent) {
            $options['args'][] = '--user-agent=' . $this->userAgent;
        }

        // Additional stealth arguments
        $options['args'][] = '--disable-dev-shm-usage';
        $options['args'][] = '--no-first-run';
        $options['args'][] = '--no-default-browser-check';
        $options['args'][] = '--no-sandbox';

        // Disable automation-related preferences
        if ($this->modifyPermissions) {
            $options['prefs'] = [
                'credentials_enable_service' => false,
                'profile.password_manager_enabled' => false,
            ];
        }

        return $options;
    }

    /**
     * Get JavaScript code to inject for hiding Selenium
     */
    public function getStealthScript(): string
    {
        if (!$this->enabled || !$this->hideWebdriver) {
            return '';
        }

        return <<<'JAVASCRIPT'
// Hide webdriver property
Object.defineProperty(navigator, 'webdriver', {
    get: () => undefined,
    configurable: true
});

// Override the Chrome object
window.chrome = {
    runtime: {}
};

// Override permissions
const originalQuery = window.navigator.permissions.query;
window.navigator.permissions.query = (parameters) => (
    parameters.name === 'notifications' ?
        Promise.resolve({ state: Notification.permission }) :
        originalQuery(parameters)
);

// Mock plugins to appear more natural
Object.defineProperty(navigator, 'plugins', {
    get: () => [1, 2, 3, 4, 5],
    configurable: true
});

// Mock languages
Object.defineProperty(navigator, 'languages', {
    get: () => ['en-US', 'en'],
    configurable: true
});

// Override toString methods that expose automation
const originalToString = Function.prototype.toString;
Function.prototype.toString = function() {
    if (this === window.navigator.permissions.query) {
        return 'function query() { [native code] }';
    }
    return originalToString.call(this);
};
JAVASCRIPT;
    }

    /**
     * Create a default stealth configuration (enabled)
     */
    public static function default(): self
    {
        return new self(
            enabled: true,
            hideWebdriver: true,
            hideAutomation: true,
            userAgent: null,
            disableInfobars: true,
            excludeSwitches: true,
            modifyPermissions: true
        );
    }

    /**
     * Create a disabled stealth configuration
     */
    public static function disabled(): self
    {
        return new self(enabled: false);
    }

    /**
     * Create a custom stealth configuration
     * 
     * @param array $options Configuration options
     * @return self
     */
    public static function custom(array $options): self
    {
        return new self(
            enabled: $options['enabled'] ?? true,
            hideWebdriver: $options['hideWebdriver'] ?? true,
            hideAutomation: $options['hideAutomation'] ?? true,
            userAgent: $options['userAgent'] ?? null,
            disableInfobars: $options['disableInfobars'] ?? true,
            excludeSwitches: $options['excludeSwitches'] ?? true,
            modifyPermissions: $options['modifyPermissions'] ?? true
        );
    }

    /**
     * Merge stealth options with existing Chrome options
     */
    public function mergeWithChromeOptions(array $existingOptions): array
    {
        if (!$this->enabled) {
            return $existingOptions;
        }

        $stealthOptions = $this->getChromeOptions();
        
        // Merge args
        if (isset($existingOptions['args'])) {
            $stealthOptions['args'] = array_unique(array_merge(
                $existingOptions['args'],
                $stealthOptions['args']
            ));
        }
        
        // Merge excludeSwitches
        if (isset($existingOptions['excludeSwitches'])) {
            $stealthOptions['excludeSwitches'] = array_unique(array_merge(
                $existingOptions['excludeSwitches'],
                $stealthOptions['excludeSwitches']
            ));
        }
        
        // Merge prefs
        if (isset($existingOptions['prefs'])) {
            $stealthOptions['prefs'] = array_merge(
                $existingOptions['prefs'],
                $stealthOptions['prefs']
            );
        }

        return array_merge($existingOptions, $stealthOptions);
    }

    /**
     * Get a natural-looking user agent string
     */
    public static function getDefaultUserAgent(): string
    {
        return 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
    }
}
