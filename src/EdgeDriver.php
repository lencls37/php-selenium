<?php

namespace Lencls37\PhpSelenium;

use GuzzleHttp\Client;
use RuntimeException;
use ZipArchive;

/**
 * Microsoft Edge driver implementation
 */
class EdgeDriver extends BrowserDriver
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
        $this->browserPath = $this->getProjectRoot() . '/edge';
    }

    public function initialize(): void
    {
        if (!is_dir($this->driverPath)) {
            mkdir($this->driverPath, 0755, true);
        }

        $edgeVersion = $this->detectBrowserVersion();
        
        if ($edgeVersion === null) {
            echo "Microsoft Edge not found on the system.\n";
            echo "Note: Please install Edge manually from https://www.microsoft.com/edge/\n";
            throw new RuntimeException("Edge not found.");
        }
        
        echo "Edge version detected: $edgeVersion\n";
        
        $this->downloadEdgeDriver($edgeVersion);
        
        echo "EdgeDriver setup completed successfully.\n";
    }

    public function getDriverPath(): string
    {
        $driverName = $this->os === 'windows' ? 'msedgedriver.exe' : 'msedgedriver';
        return $this->driverPath . '/' . $driverName;
    }

    public function getBrowserPath(): ?string
    {
        $edgePaths = $this->getEdgePaths();
        
        foreach ($edgePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        
        return null;
    }

    protected function detectBrowserVersion(): ?string
    {
        $edgePath = $this->getBrowserPath();
        
        if ($edgePath === null) {
            return null;
        }
        
        try {
            $output = shell_exec("\"$edgePath\" --version 2>&1");
            
            if ($output && preg_match('/(\d+\.\d+\.\d+\.\d+)/', $output, $matches)) {
                return $matches[1];
            } elseif ($output && preg_match('/(\d+\.\d+\.\d+)/', $output, $matches)) {
                return $matches[1];
            }
        } catch (\Exception $e) {
            // Silently fail
        }
        
        return null;
    }

    private function getEdgePaths(): array
    {
        switch ($this->os) {
            case 'windows':
                return [
                    'C:/Program Files/Microsoft/Edge/Application/msedge.exe',
                    'C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe',
                ];
            case 'mac':
                return [
                    '/Applications/Microsoft Edge.app/Contents/MacOS/Microsoft Edge',
                ];
            case 'linux':
                return [
                    '/usr/bin/microsoft-edge',
                    '/usr/bin/microsoft-edge-stable',
                ];
            default:
                return [];
        }
    }

    private function downloadEdgeDriver(string $edgeVersion): void
    {
        echo "Downloading EdgeDriver for Edge version $edgeVersion...\n";
        
        $majorVersion = explode('.', $edgeVersion)[0];
        
        // Edge uses similar versioning to Chrome
        $platform = $this->getEdgeDriverPlatform();
        
        // Try Edge for Testing API
        try {
            $url = 'https://msedgewebdriverstorage.blob.core.windows.net/edgewebdriver/LATEST_STABLE';
            $response = $this->httpClient->get($url);
            $driverVersion = trim($response->getBody()->getContents());
            
            $downloadUrl = "https://msedgedriver.azureedge.net/$driverVersion/edgedriver_$platform.zip";
            
            $zipFile = $this->driverPath . '/edgedriver.zip';
            
            echo "Downloading from: $downloadUrl\n";
            $this->httpClient->get($downloadUrl, ['sink' => $zipFile]);
            
            echo "Extracting EdgeDriver...\n";
            $this->extractZip($zipFile, $this->driverPath);
            
            unlink($zipFile);
            
            if ($this->os !== 'windows') {
                $driverFile = $this->driverPath . '/msedgedriver';
                if (file_exists($driverFile)) {
                    chmod($driverFile, 0755);
                }
            }
            
            echo "EdgeDriver installed successfully at: " . $this->getDriverPath() . "\n";
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to download EdgeDriver: " . $e->getMessage());
        }
    }

    private function getEdgeDriverPlatform(): string
    {
        switch ($this->os) {
            case 'windows':
                return 'win64';
            case 'mac':
                return 'mac64';
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
