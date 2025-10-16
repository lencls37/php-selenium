<?php

/**
 * Form Filling Example
 * 
 * This example demonstrates how to:
 * - Navigate to a page with a form
 * - Find form elements
 * - Fill in input fields
 * - Submit the form
 * - Wait for results
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Lencls37\PhpSelenium\ChromeDriver;
use Lencls37\PhpSelenium\WebDriver;

echo "Form Filling Example\n";
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
    
    // Example: Complex Form
    echo "Form Filling Demonstration\n";
    echo "--------------------------\n";
    
    // Create a simple HTML form for demonstration
    $htmlContent = <<<HTML
<!DOCTYPE html>
<html>
<head><title>Demo Form</title></head>
<body>
    <h1>Registration Form</h1>
    <form id="registrationForm" action="/submit" method="post">
        <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div>
            <input type="checkbox" id="terms" name="terms">
            <label for="terms">I agree to terms</label>
        </div>
        <button type="submit" id="submitBtn">Register</button>
    </form>
    <div id="result" style="display:none; margin-top: 20px;">
        <h2>Form Submitted!</h2>
        <p>Thank you for registering.</p>
    </div>
    <script>
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            document.getElementById('result').style.display = 'block';
        });
    </script>
</body>
</html>
HTML;
    
    // Save HTML to a temporary file
    $htmlFile = '/tmp/demo_form.html';
    file_put_contents($htmlFile, $htmlContent);
    
    // Navigate to the form
    $driver->get('file://' . $htmlFile);
    echo "✓ Loaded demo form\n";
    
    // Fill in the username field
    $usernameField = $driver->findElementById('username');
    $usernameField->sendKeys('johndoe');
    echo "✓ Filled username: johndoe\n";
    
    // Fill in the email field
    $emailField = $driver->findElementById('email');
    $emailField->sendKeys('john.doe@example.com');
    echo "✓ Filled email: john.doe@example.com\n";
    
    // Fill in the password field
    $passwordField = $driver->findElementById('password');
    $passwordField->sendKeys('SecurePassword123!');
    echo "✓ Filled password\n";
    
    // Check the terms checkbox
    $termsCheckbox = $driver->findElementById('terms');
    $termsCheckbox->click();
    echo "✓ Accepted terms\n";
    
    // Verify checkbox is selected
    if ($termsCheckbox->isSelected()) {
        echo "✓ Checkbox is checked\n";
    }
    
    // Take screenshot before submission
    $driver->saveScreenshot('/tmp/form_filled.png');
    echo "✓ Screenshot of filled form saved\n";
    
    // Submit the form
    $submitButton = $driver->findElementById('submitBtn');
    $submitButton->click();
    echo "✓ Form submitted\n";
    
    // Wait for result message
    sleep(1);
    
    // Check if result is displayed
    $resultDiv = $driver->findElementById('result');
    if ($resultDiv->isDisplayed()) {
        echo "✓ Success message displayed\n";
        echo "  Message: " . $resultDiv->getText() . "\n";
    }
    
    // Take screenshot of result
    $driver->saveScreenshot('/tmp/form_result.png');
    echo "✓ Screenshot of result saved\n\n";
    
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "Form filling example completed successfully!\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";
    
    echo "Screenshots saved:\n";
    echo "  - /tmp/form_filled.png\n";
    echo "  - /tmp/form_result.png\n\n";
    
    // Clean up
    $driver->quit();
    
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    if (isset($driver)) {
        $driver->quit();
    }
    exit(1);
}
