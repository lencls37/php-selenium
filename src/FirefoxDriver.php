<?php

namespace Lencls37\PhpSelenium;

use GuzzleHttp\Client;
use RuntimeException;
use ZipArchive;

/**
 * Firefox/Gecko driver implementation
 */
class FirefoxDriver extends BrowserDriver
{
    private Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout' => 300,
            'verify' => false
        ]);
        $this->os = $this->detectOS();
        $this->driverPath = $this->getProjectRoot() . '/drivers';
        $this->browserPath = $this->getProjectRoot() . '/firefox';
    }

    public function initialize(): void
    {
        if (!is_dir($this->driverPath)) {
            mkdir($this->driverPath, 0755, true);
        }

        $firefoxVersion = $this->detectBrowserVersion();
        
        if ($firefoxVersion === null) {
            echo "Firefox not found on the system.\n";
            echo "Note: Please install Firefox manually from https://www.mozilla.org/firefox/\n";
            throw new RuntimeException("Firefox not found.");
        }
        
        echo "Firefox version detected: $firefoxVersion\n";
        
        $this->downloadGeckoDriver();
        
        echo "GeckoDriver setup completed successfully.\n";
    }

    public function getDriverPath(): string
    {
        $driverName = $this->os === 'windows' ? 'geckodriver.exe' : 'geckodriver';
        return $this->driverPath . '/' . $driverName;
    }

    public function getBrowserPath(): ?string
    {
        $firefoxPaths = $this->getFirefoxPaths();
        
        foreach ($firefoxPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        
        return null;
    }

    protected function detectBrowserVersion(): ?string
    {
        $firefoxPath = $this->getBrowserPath();
        
        if ($firefoxPath === null) {
            return null;
        }
        
        try {
            $output = shell_exec("\"$firefoxPath\" --version 2>&1");
            
            if ($output && preg_match('/(\d+\.\d+)/', $output, $matches)) {
                return $matches[1];
            }
        } catch (\Exception $e) {
            // Silently fail
        }
        
        return null;
    }

    private function getFirefoxPaths(): array
    {
        switch ($this->os) {
            case 'windows':
                return [
                    'C:/Program Files/Mozilla Firefox/firefox.exe',
                    'C:/Program Files (x86)/Mozilla Firefox/firefox.exe',
                ];
            case 'mac':
                return [
                    '/Applications/Firefox.app/Contents/MacOS/firefox',
                ];
            case 'linux':
                return [
                    '/usr/bin/firefox',
                    '/usr/bin/firefox-esr',
                    '/snap/bin/firefox',
                ];
            default:
                return [];
        }
    }

    private function downloadGeckoDriver(): void
    {
        echo "Downloading GeckoDriver...\n";
        
        // Get latest GeckoDriver release
        $url = 'https://api.github.com/repos/mozilla/geckodriver/releases/latest';
        
        try {
            $response = $this->httpClient->get($url);
            $data = json_decode($response->getBody()->getContents(), true);
            
            $platform = $this->getGeckoDriverPlatform();
            $downloadUrl = null;
            
            foreach ($data['assets'] as $asset) {
                if (strpos($asset['name'], $platform) !== false) {
                    $downloadUrl = $asset['browser_download_url'];
                    break;
                }
            }
            
            if ($downloadUrl === null) {
                throw new RuntimeException("Could not find GeckoDriver for platform: $platform");
            }
            
            $archiveFile = $this->driverPath . '/geckodriver.' . ($this->os === 'windows' ? 'zip' : 'tar.gz');
            
            echo "Downloading from: $downloadUrl\n";
            $this->httpClient->get($downloadUrl, ['sink' => $archiveFile]);
            
            echo "Extracting GeckoDriver...\n";
            
            if ($this->os === 'windows') {
                $this->extractZip($archiveFile, $this->driverPath);
            } else {
                exec("tar -xzf \"$archiveFile\" -C \"{$this->driverPath}\" 2>&1");
            }
            
            unlink($archiveFile);
            
            if ($this->os !== 'windows') {
                chmod($this->driverPath . '/geckodriver', 0755);
            }
            
            echo "GeckoDriver installed successfully at: " . $this->getDriverPath() . "\n";
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to download GeckoDriver: " . $e->getMessage());
        }
    }

    private function getGeckoDriverPlatform(): string
    {
        switch ($this->os) {
            case 'windows':
                return 'win64';
            case 'mac':
                return 'macos';
            case 'linux':
                return 'linux64';
            default:
                throw new RuntimeException("Unsupported platform: {$this->os}");
        }
    }

    private function extractZip(string $zipFile, string $destination): void
    {
        $zip = new ZipArchive();
        
        if ($zip->open($zipFile) === true) {
            $zip->extractTo($destination);
            $zip->close();
        } else {
            throw new RuntimeException("Failed to extract ZIP file: $zipFile");
        }
    }
}
