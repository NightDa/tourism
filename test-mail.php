<?php
// Include required files - ONLY include, don't redefine functions
require_once __DIR__ . '/vendor/autoload.php';
require_once 'includes/logger.php';
require_once 'includes/mail_phpmailer.php'; // This already contains the functions

// Simple HTML styling
echo '<!DOCTYPE html>
<html>
<head>
    <title>Travol Morocco - Mail Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
        .success { color: green; background: #e8f5e9; padding: 15px; border-radius: 5px; }
        .error { color: red; background: #ffebee; padding: 15px; border-radius: 5px; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; }
        h1 { color: #333; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>📧 Travol Morocco Mail Test</h1>';

// Your email address
$test_email = "mhamed.elouardani@gmail.com";

echo "<p>Testing mail to: <strong>$test_email</strong></p>";

// Test 1: Basic mail function
echo "<h2>Test 1: PHPMailer Test</h2>";

if (function_exists('testMail')) {
    if (testMail($test_email)) {
        echo '<div class="success">✅ Test email sent successfully! Check your inbox.</div>';
    } else {
        echo '<div class="error">❌ Failed to send test email. Check PHPMailer configuration.</div>';
    }
} else {
    echo '<div class="error">❌ testMail function not found!</div>';
}

// Test 2: Contact notification function
echo "<h2>Test 2: Contact Notification Function</h2>";

$test_contact = [
    'name' => 'Test User',
    'email' => 'test@example.com',
    'phone' => '+212600000000',
    'subject' => 'Test Subject',
    'message' => 'This is a test message from the mail test script.',
    'created_at' => date('Y-m-d H:i:s'),
    'ip' => $_SERVER['REMOTE_ADDR']
];

if (function_exists('sendContactNotification')) {
    if (sendContactNotification($test_contact)) {
        echo '<div class="success">✅ Contact notification sent!</div>';
    } else {
        echo '<div class="error">❌ Contact notification failed.</div>';
    }
} else {
    echo '<div class="error">❌ sendContactNotification function not found!</div>';
}

// Test 3: File Checks
echo "<h2>Test 3: File Checks</h2>";

$mail_file = 'includes/mail_phpmailer.php';
if (file_exists($mail_file)) {
    echo "<div class='success'>✅ includes/mail_phpmailer.php exists</div>";
    echo "<pre>File path: " . realpath($mail_file) . "</pre>";
} else {
    echo "<div class='error'>❌ includes/mail_phpmailer.php NOT found!</div>";
}

$logger_file = 'includes/logger.php';
if (file_exists($logger_file)) {
    echo "<div class='success'>✅ includes/logger.php exists</div>";
} else {
    echo "<div class='error'>❌ includes/logger.php NOT found!</div>";
}

// Test 4: Check vendor/autoload.php
echo "<h2>Test 4: Composer Autoload</h2>";

if (file_exists('vendor/autoload.php')) {
    echo "<div class='success'>✅ vendor/autoload.php exists</div>";
} else {
    echo "<div class='error'>❌ vendor/autoload.php NOT found! Run 'composer install'</div>";
}

// Test 5: Manual Test Link
echo "<h2>Test 5: Manual Test</h2>";
echo '<div class="info">';
echo '<p><strong>Try this:</strong> Click the link below to test the contact form manually:</p>';
echo '<p><a href="pages/contact.php" target="_blank">➡️ Go to Contact Page</a></p>';
echo '<p>Fill out the form and submit. Then check:</p>';
echo '<ul>';
echo '<li><strong>data/contacts.json</strong> - Should contain your submission</li>';
echo '<li><strong>logs/</strong> folder - Should have log entries</li>';
echo '<li><strong>Your email inbox</strong> - Should receive notification</li>';
echo '</ul>';
echo '</div>';

echo '</body></html>';
