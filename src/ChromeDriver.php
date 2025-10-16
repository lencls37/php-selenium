<?php

namespace Lencls37\PhpSelenium;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use ZipArchive;
use RuntimeException;

/**
 * Chrome driver implementation
 */
class ChromeDriver
{
    private string $driverPath;
    private string $chromePath;
    private string $os;
    private Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout' => 300,
            'verify' => false
        ]);
        $this->os = $this->detectOS();
        $this->driverPath = $this->getProjectRoot() . '/drivers';
        $this->chromePath = $this->getProjectRoot() . '/chrome';
    }

    /**
     * Initialize the Selenium driver
     */
    public function initialize(): void
    {
        $this->ensureDriversDirectory();
        
        $chromeVersion = $this->detectChromeVersion();
        
        if ($chromeVersion === null) {
            echo "Chrome/Chromium not found on the system.\n";
            echo "Chrome indirilsin mi? (y/n): ";
            
            $handle = fopen("php://stdin", "r");
            $response = strtolower(trim(fgets($handle)));
            fclose($handle);
            
            if ($response !== 'y') {
                throw new RuntimeException("Chrome installation cancelled by user.");
            }
            
            $this->downloadAndInstallChrome();
            $chromeVersion = $this->detectChromeVersion();
            
            if ($chromeVersion === null) {
                throw new RuntimeException("Failed to install Chrome.");
            }
        }
        
        echo "Chrome version detected: $chromeVersion\n";
        
        // Check if driver already exists
        $driverPath = $this->getDriverPath();
        if (file_exists($driverPath)) {
            echo "ChromeDriver already exists at: $driverPath\n";
            echo "ChromeDriver setup completed successfully.\n";
            return;
        }
        
        try {
            $this->downloadChromeDriver($chromeVersion);
            echo "ChromeDriver setup completed successfully.\n";
        } catch (RuntimeException $e) {
            echo "Warning: " . $e->getMessage() . "\n";
            echo "Note: In environments with internet access, ChromeDriver would be downloaded automatically.\n";
            echo "You can manually download ChromeDriver from: https://chromedriver.chromium.org/downloads\n";
        }
    }

    /**
     * Get the path to the ChromeDriver executable
     */
    public function getDriverPath(): string
    {
        $driverName = $this->os === 'windows' ? 'chromedriver.exe' : 'chromedriver';
        return $this->driverPath . '/' . $driverName;
    }

    /**
     * Get the path to the Chrome executable
     */
    public function getChromePath(): ?string
    {
        // Try to find Chrome in standard locations
        $chromePaths = $this->getChromePaths();
        
        foreach ($chromePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        
        // Check if we downloaded Chrome
        $downloadedChrome = $this->getDownloadedChromePath();
        if ($downloadedChrome && file_exists($downloadedChrome)) {
            return $downloadedChrome;
        }
        
        return null;
    }

    /**
     * Detect the operating system
     */
    private function detectOS(): string
    {
        $os = strtolower(PHP_OS);
        
        if (strpos($os, 'win') !== false) {
            return 'windows';
        } elseif (strpos($os, 'darwin') !== false) {
            return 'mac';
        } elseif (strpos($os, 'linux') !== false) {
            return 'linux';
        }
        
        throw new RuntimeException("Unsupported operating system: $os");
    }

    /**
     * Get standard Chrome installation paths based on OS
     */
    private function getChromePaths(): array
    {
        switch ($this->os) {
            case 'windows':
                return [
                    'C:/Program Files/Google/Chrome/Application/chrome.exe',
                    'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe',
                    getenv('LOCALAPPDATA') . '/Google/Chrome/Application/chrome.exe',
                ];
            case 'mac':
                return [
                    '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
                    '/Applications/Chromium.app/Contents/MacOS/Chromium',
                ];
            case 'linux':
                return [
                    '/usr/bin/google-chrome',
                    '/usr/bin/google-chrome-stable',
                    '/usr/bin/chromium',
                    '/usr/bin/chromium-browser',
                    '/snap/bin/chromium',
                ];
            default:
                return [];
        }
    }

    /**
     * Get the path to downloaded Chrome
     */
    private function getDownloadedChromePath(): ?string
    {
        switch ($this->os) {
            case 'windows':
                return $this->chromePath . '/chrome.exe';
            case 'mac':
                return $this->chromePath . '/Google Chrome.app/Contents/MacOS/Google Chrome';
            case 'linux':
                return $this->chromePath . '/chrome';
            default:
                return null;
        }
    }

    /**
     * Detect Chrome version
     */
    private function detectChromeVersion(): ?string
    {
        $chromePath = $this->getChromePath();
        
        if ($chromePath === null) {
            return null;
        }
        
        try {
            switch ($this->os) {
                case 'windows':
                    $output = shell_exec("wmic datafile where name=\"" . str_replace('/', '\\\\', $chromePath) . "\" get Version /value 2>&1");
                    if (preg_match('/Version=(\d+\.\d+\.\d+\.\d+)/', $output, $matches)) {
                        return $matches[1];
                    }
                    // Alternative method
                    $output = shell_exec("\"$chromePath\" --version 2>&1");
                    break;
                case 'mac':
                case 'linux':
                    $output = shell_exec("\"$chromePath\" --version 2>&1");
                    break;
                default:
                    return null;
            }
            
            if ($output && preg_match('/(\d+\.\d+\.\d+\.\d+)/', $output, $matches)) {
                return $matches[1];
            } elseif ($output && preg_match('/(\d+\.\d+\.\d+)/', $output, $matches)) {
                return $matches[1];
            }
        } catch (\Exception $e) {
            // Silently fail and return null
        }
        
        return null;
    }

    /**
     * Download and install Chrome
     */
    private function downloadAndInstallChrome(): void
    {
        echo "Downloading Chrome for {$this->os}...\n";
        
        if (!is_dir($this->chromePath)) {
            mkdir($this->chromePath, 0755, true);
        }
        
        switch ($this->os) {
            case 'linux':
                $this->downloadChromeLinux();
                break;
            case 'mac':
                $this->downloadChromeMac();
                break;
            case 'windows':
                $this->downloadChromeWindows();
                break;
        }
        
        echo "Chrome downloaded successfully.\n";
    }

    /**
     * Download Chrome for Linux
     */
    private function downloadChromeLinux(): void
    {
        $url = 'https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb';
        $debFile = $this->chromePath . '/chrome.deb';
        
        echo "Downloading from: $url\n";
        $this->downloadFile($url, $debFile);
        
        // Extract the deb package
        echo "Extracting Chrome...\n";
        exec("dpkg-deb -x \"$debFile\" \"{$this->chromePath}\" 2>&1", $output, $returnCode);
        
        if ($returnCode === 0) {
            // Create a symlink to the chrome binary
            $chromeBinary = $this->chromePath . '/opt/google/chrome/chrome';
            $symlinkPath = $this->chromePath . '/chrome';
            
            if (file_exists($chromeBinary)) {
                symlink($chromeBinary, $symlinkPath);
            }
        }
        
        unlink($debFile);
    }

    /**
     * Download Chrome for Mac
     */
    private function downloadChromeMac(): void
    {
        // Chrome for Testing is a better option for automation
        $url = 'https://googlechromelabs.github.io/chrome-for-testing/last-known-good-versions.json';
        
        try {
            $response = $this->httpClient->get($url);
            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['channels']['Stable']['downloads']['chrome'])) {
                foreach ($data['channels']['Stable']['downloads']['chrome'] as $download) {
                    if ($download['platform'] === 'mac-x64') {
                        $chromeUrl = $download['url'];
                        $zipFile = $this->chromePath . '/chrome.zip';
                        
                        echo "Downloading from: $chromeUrl\n";
                        $this->downloadFile($chromeUrl, $zipFile);
                        
                        echo "Extracting Chrome...\n";
                        $this->extractZip($zipFile, $this->chromePath);
                        unlink($zipFile);
                        return;
                    }
                }
            }
        } catch (\Exception $e) {
            echo "Warning: Could not download Chrome for Testing: {$e->getMessage()}\n";
        }
        
        echo "Note: Please install Chrome manually from https://www.google.com/chrome/\n";
    }

    /**
     * Download Chrome for Windows
     */
    private function downloadChromeWindows(): void
    {
        // Chrome for Testing is a better option for automation
        $url = 'https://googlechromelabs.github.io/chrome-for-testing/last-known-good-versions.json';
        
        try {
            $response = $this->httpClient->get($url);
            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['channels']['Stable']['downloads']['chrome'])) {
                foreach ($data['channels']['Stable']['downloads']['chrome'] as $download) {
                    if ($download['platform'] === 'win64') {
                        $chromeUrl = $download['url'];
                        $zipFile = $this->chromePath . '/chrome.zip';
                        
                        echo "Downloading from: $chromeUrl\n";
                        $this->downloadFile($chromeUrl, $zipFile);
                        
                        echo "Extracting Chrome...\n";
                        $this->extractZip($zipFile, $this->chromePath);
                        unlink($zipFile);
                        return;
                    }
                }
            }
        } catch (\Exception $e) {
            echo "Warning: Could not download Chrome for Testing: {$e->getMessage()}\n";
        }
        
        echo "Note: Please install Chrome manually from https://www.google.com/chrome/\n";
    }

    /**
     * Download ChromeDriver
     */
    private function downloadChromeDriver(string $chromeVersion): void
    {
        echo "Downloading ChromeDriver for Chrome version $chromeVersion...\n";
        
        // Extract major version
        $majorVersion = explode('.', $chromeVersion)[0];
        
        // Get the matching ChromeDriver version
        $driverVersion = $this->getChromeDriverVersion($majorVersion);
        
        if ($driverVersion === null) {
            throw new RuntimeException("Could not find matching ChromeDriver version for Chrome $chromeVersion");
        }
        
        echo "ChromeDriver version: $driverVersion\n";
        
        // Determine platform
        $platform = $this->getChromeDriverPlatform();
        
        // Build download URL
        $url = "https://storage.googleapis.com/chrome-for-testing-public/$driverVersion/$platform/chromedriver-$platform.zip";
        
        $zipFile = $this->driverPath . '/chromedriver.zip';
        
        echo "Downloading from: $url\n";
        
        try {
            $this->downloadFile($url, $zipFile);
            
            echo "Extracting ChromeDriver...\n";
            $this->extractZip($zipFile, $this->driverPath);
            
            // Move chromedriver from subdirectory if needed
            $extractedDir = $this->driverPath . "/chromedriver-$platform";
            if (is_dir($extractedDir)) {
                $driverFile = $this->os === 'windows' ? 'chromedriver.exe' : 'chromedriver';
                if (file_exists($extractedDir . '/' . $driverFile)) {
                    rename($extractedDir . '/' . $driverFile, $this->driverPath . '/' . $driverFile);
                }
                // Clean up extracted directory
                $this->deleteDirectory($extractedDir);
            }
            
            unlink($zipFile);
            
            // Make executable on Unix systems
            if ($this->os !== 'windows') {
                chmod($this->driverPath . '/chromedriver', 0755);
            }
            
            echo "ChromeDriver installed successfully at: " . $this->getDriverPath() . "\n";
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to download ChromeDriver: " . $e->getMessage());
        }
    }

    /**
     * Get ChromeDriver version for given Chrome version
     */
    private function getChromeDriverVersion(string $chromeMajorVersion): ?string
    {
        try {
            // For Chrome 115+, use Chrome for Testing API
            if ((int)$chromeMajorVersion >= 115) {
                $url = 'https://googlechromelabs.github.io/chrome-for-testing/last-known-good-versions-with-downloads.json';
                $response = $this->httpClient->get($url);
                $data = json_decode($response->getBody()->getContents(), true);
                
                if (isset($data['channels']['Stable']['version'])) {
                    return $data['channels']['Stable']['version'];
                }
            } else {
                // For older Chrome versions, use ChromeDriver version API
                $url = "https://chromedriver.storage.googleapis.com/LATEST_RELEASE_$chromeMajorVersion";
                $response = $this->httpClient->get($url);
                return trim($response->getBody()->getContents());
            }
        } catch (GuzzleException $e) {
            echo "Warning: Could not fetch ChromeDriver version: {$e->getMessage()}\n";
            echo "Note: This may be due to network restrictions in the current environment.\n";
            echo "In a production environment with internet access, ChromeDriver would be downloaded automatically.\n";
        }
        
        return null;
    }

    /**
     * Get ChromeDriver platform string
     */
    private function getChromeDriverPlatform(): string
    {
        switch ($this->os) {
            case 'windows':
                return 'win64';
            case 'mac':
                return 'mac-x64';
            case 'linux':
                return 'linux64';
            default:
                throw new RuntimeException("Unsupported platform: {$this->os}");
        }
    }

    /**
     * Download a file from URL
     */
    private function downloadFile(string $url, string $destination): void
    {
        try {
            $response = $this->httpClient->get($url, [
                'sink' => $destination,
                'progress' => function ($downloadTotal, $downloadedBytes) {
                    if ($downloadTotal > 0) {
                        $percent = ($downloadedBytes / $downloadTotal) * 100;
                        echo "\rProgress: " . number_format($percent, 2) . "%";
                    }
                }
            ]);
            echo "\n";
        } catch (GuzzleException $e) {
            throw new RuntimeException("Failed to download file from $url: " . $e->getMessage());
        }
    }

    /**
     * Extract ZIP archive
     */
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

    /**
     * Ensure drivers directory exists
     */
    private function ensureDriversDirectory(): void
    {
        if (!is_dir($this->driverPath)) {
            mkdir($this->driverPath, 0755, true);
        }
    }

    /**
     * Get project root directory
     */
    private function getProjectRoot(): string
    {
        // Try to find composer.json
        $dir = __DIR__;
        while ($dir !== '/') {
            if (file_exists($dir . '/composer.json')) {
                return $dir;
            }
            $dir = dirname($dir);
        }
        
        // Fallback to parent directory of src
        return dirname(__DIR__);
    }

    /**
     * Delete directory recursively
     */
    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
