<?php

/**
 * Web Scraping Example
 * 
 * This example demonstrates how to:
 * - Navigate to a webpage
 * - Find multiple elements
 * - Extract data from elements
 * - Handle dynamic content
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Lencls37\PhpSelenium\ChromeDriver;
use Lencls37\PhpSelenium\WebDriver;

echo "Web Scraping Example\n";
echo "====================\n\n";

try {
    // Setup
    echo "Setting up browser...\n";
    $seleniumDriver = new ChromeDriver();
    $seleniumDriver->initialize();
    
    $driver = new WebDriver($seleniumDriver->getDriverPath(), 9515, [
        'goog:chromeOptions' => [
            'binary' => $seleniumDriver->getChromePath(),
            'args' => ['--headless', '--disable-gpu', '--no-sandbox']
        ]
    ]);
    $driver->start();
    echo "✓ Browser started\n\n";
    
    // Example: Scrape example.com
    echo "Scraping example.com\n";
    echo "--------------------\n";
    
    $driver->get('https://example.com');
    echo "✓ Navigated to example.com\n";
    
    // Get page title
    $title = $driver->getTitle();
    echo "✓ Page Title: $title\n";
    
    // Get main heading
    $heading = $driver->findElementByTagName('h1');
    $headingText = $heading->getText();
    echo "✓ Main Heading: $headingText\n";
    
    // Get all paragraphs
    $paragraphs = $driver->findElementsByTagName('p');
    echo "✓ Found " . count($paragraphs) . " paragraph(s)\n";
    
    foreach ($paragraphs as $index => $paragraph) {
        $text = $paragraph->getText();
        echo "  Paragraph " . ($index + 1) . ": " . substr($text, 0, 50);
        if (strlen($text) > 50) {
            echo "...";
        }
        echo "\n";
    }
    
    // Get all links
    $links = $driver->findElementsByTagName('a');
    echo "✓ Found " . count($links) . " link(s)\n";
    
    foreach ($links as $link) {
        $text = $link->getText();
        $href = $link->getAttribute('href');
        echo "  - $text: $href\n";
    }
    
    echo "\n";
    
    // Example: Create and scrape a dynamic page
    echo "Scraping Dynamic Content\n";
    echo "------------------------\n";
    
    $htmlContent = <<<HTML
<!DOCTYPE html>
<html>
<head><title>Product List</title></head>
<body>
    <h1>Products</h1>
    <div id="products">
        <div class="product" data-id="1">
            <h2 class="product-name">Laptop</h2>
            <p class="product-price">$999.99</p>
            <p class="product-stock">In Stock</p>
        </div>
        <div class="product" data-id="2">
            <h2 class="product-name">Keyboard</h2>
            <p class="product-price">$79.99</p>
            <p class="product-stock">Out of Stock</p>
        </div>
        <div class="product" data-id="3">
            <h2 class="product-name">Mouse</h2>
            <p class="product-price">$29.99</p>
            <p class="product-stock">In Stock</p>
        </div>
    </div>
</body>
</html>
HTML;
    
    $htmlFile = '/tmp/products.html';
    file_put_contents($htmlFile, $htmlContent);
    
    $driver->get('file://' . $htmlFile);
    echo "✓ Loaded product page\n";
    
    // Find all products
    $products = $driver->findElementsByCssSelector('.product');
    echo "✓ Found " . count($products) . " products\n\n";
    
    $productsData = [];
    
    foreach ($products as $index => $product) {
        // Extract product data
        $id = $product->getAttribute('data-id');
        $name = $product->findElementByCssSelector('.product-name')->getText();
        $price = $product->findElementByCssSelector('.product-price')->getText();
        $stock = $product->findElementByCssSelector('.product-stock')->getText();
        
        $productsData[] = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'stock' => $stock
        ];
        
        echo "Product " . ($index + 1) . ":\n";
        echo "  ID: $id\n";
        echo "  Name: $name\n";
        echo "  Price: $price\n";
        echo "  Stock: $stock\n\n";
    }
    
    // Example: Using XPath for complex queries
    echo "Using XPath Queries\n";
    echo "-------------------\n";
    
    // Find products in stock using XPath
    $inStockProducts = $driver->findElementsByXPath('//div[@class="product"][.//p[@class="product-stock" and contains(text(), "In Stock")]]');
    echo "✓ Found " . count($inStockProducts) . " products in stock\n";
    
    foreach ($inStockProducts as $product) {
        $name = $product->findElementByCssSelector('.product-name')->getText();
        echo "  - $name\n";
    }
    
    echo "\n";
    
    // Example: Execute JavaScript to get computed data
    echo "Using JavaScript for Data Extraction\n";
    echo "-------------------------------------\n";
    
    // Get all product names using JavaScript
    $productNames = $driver->executeScript('
        return Array.from(document.querySelectorAll(".product-name"))
            .map(el => el.textContent);
    ');
    
    echo "✓ Product names via JavaScript:\n";
    foreach ($productNames as $name) {
        echo "  - $name\n";
    }
    
    echo "\n";
    
    // Calculate total value of in-stock items
    $totalValue = $driver->executeScript('
        let total = 0;
        document.querySelectorAll(".product").forEach(product => {
            const stock = product.querySelector(".product-stock").textContent;
            if (stock === "In Stock") {
                const priceText = product.querySelector(".product-price").textContent;
                const price = parseFloat(priceText.replace("$", ""));
                total += price;
            }
        });
        return total;
    ');
    
    echo "✓ Total value of in-stock items: $" . number_format($totalValue, 2) . "\n\n";
    
    // Save scraped data to JSON
    $jsonFile = '/tmp/scraped_products.json';
    file_put_contents($jsonFile, json_encode($productsData, JSON_PRETTY_PRINT));
    echo "✓ Scraped data saved to: $jsonFile\n";
    
    // Take screenshot
    $driver->saveScreenshot('/tmp/products_page.png');
    echo "✓ Screenshot saved to: /tmp/products_page.png\n\n";
    
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "Web scraping example completed successfully!\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";
    
    echo "Output files:\n";
    echo "  - /tmp/scraped_products.json (scraped data)\n";
    echo "  - /tmp/products_page.png (screenshot)\n\n";
    
    // Clean up
    $driver->quit();
    
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    if (isset($driver)) {
        $driver->quit();
    }
    exit(1);
}
