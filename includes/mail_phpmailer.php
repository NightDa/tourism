<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'logger.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Send contact form notification using Gmail SMTP
 */
function sendContactNotification($contact)
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mhamed.elouardani@gmail.com'; // Your Gmail
        $mail->Password   = 'icak emfk pqei qkwv'; // Your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('noreply@travolmorocco.com', 'Travol Morocco');
        $mail->addAddress('mhamed.elouardani@gmail.com'); // Your email
        $mail->addReplyTo($contact['email'], $contact['name']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Contact: ' . $contact['subject'];

        $mail->Body = "
        <html>
        <head><style>
            body { font-family: Arial, sans-serif; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; }
            .content { padding: 20px; }
            .field { margin: 15px 0; }
            .label { font-weight: bold; color: #667eea; }
        </style></head>
        <body>
            <div class='header'>
                <h2>New Contact Form Submission</h2>
            </div>
            <div class='content'>
                <div class='field'><span class='label'>Name:</span> {$contact['name']}</div>
                <div class='field'><span class='label'>Email:</span> {$contact['email']}</div>
                <div class='field'><span class='label'>Phone:</span> {$contact['phone']}</div>
                <div class='field'><span class='label'>Subject:</span> {$contact['subject']}</div>
                <div class='field'><span class='label'>Message:</span><br>{$contact['message']}</div>
            </div>
        </body>
        </html>
        ";

        $mail->AltBody = "New Contact Form Submission\n\n" .
            "Name: {$contact['name']}\n" .
            "Email: {$contact['email']}\n" .
            "Phone: {$contact['phone']}\n" .
            "Subject: {$contact['subject']}\n" .
            "Message: {$contact['message']}\n";

        $mail->send();
        logMessage("Contact email sent successfully", 'info');
        return true;
    } catch (Exception $e) {
        logError("PHPMailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Simple test function
 */
function testMail($to)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mhamed.elouardani@gmail.com';
        $mail->Password   = 'icak emfk pqei qkwv';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('noreply@travolmorocco.com', 'Travol Morocco');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = 'Test Email from Travol Morocco';
        $mail->Body    = '<h2>✅ Test Email</h2><p>PHPMailer is working correctly!</p>';
        $mail->AltBody = 'Test Email - PHPMailer is working!';

        return $mail->send();
    } catch (Exception $e) {
        error_log("Test mail failed: " . $mail->ErrorInfo);
        return false;
    }
}
