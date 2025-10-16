<?php

namespace Lencls37\PhpSelenium;

/**
 * Abstract base class for browser drivers
 */
abstract class BrowserDriver
{
    protected string $driverPath;
    protected string $browserPath;
    protected string $os;

    abstract public function initialize(): void;
    abstract public function getDriverPath(): string;
    abstract public function getBrowserPath(): ?string;
    abstract protected function detectBrowserVersion(): ?string;

    /**
     * Detect the operating system
     */
    protected function detectOS(): string
    {
        $os = strtolower(PHP_OS);
        
        if (strpos($os, 'win') !== false) {
            return 'windows';
        } elseif (strpos($os, 'darwin') !== false) {
            return 'mac';
        } elseif (strpos($os, 'linux') !== false) {
            return 'linux';
        }
        
        throw new \RuntimeException("Unsupported operating system: $os");
    }

    /**
     * Get project root directory
     */
    protected function getProjectRoot(): string
    {
        $dir = __DIR__;
        while ($dir !== '/') {
            if (file_exists($dir . '/composer.json')) {
                return $dir;
            }
            $dir = dirname($dir);
        }
        
        return dirname(__DIR__);
    }
}
