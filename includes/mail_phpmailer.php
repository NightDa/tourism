<?php
// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Send a test email
 */
function testMail($to)
{
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mhamed.elouardani@gmail.com';
        $mail->Password   = 'icakemfkpqeiqkwv'; // REMOVED SPACES – important!
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('noreply@travolmorocco.com', 'Travol Morocco');
        $mail->addAddress($to);

        // Content
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

/**
 * Send contact form notification – FULL HTML EMAIL
 */
function sendContactNotification($contact)
{
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mhamed.elouardani@gmail.com';
        $mail->Password   = 'icakemfkpqeiqkwv'; // no spaces
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('noreply@travolmorocco.com', 'Travol Morocco');
        $mail->addAddress('mhamed.elouardani@gmail.com'); // Admin
        $mail->addReplyTo($contact['email'], $contact['name']);

        // Build a complete HTML email
        $htmlBody = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
        .header h2 { margin: 0; font-size: 28px; }
        .content { padding: 30px; }
        .field { margin-bottom: 20px; }
        .label { font-weight: bold; color: #667eea; display: block; margin-bottom: 5px; }
        .value { background: #f8f9fa; padding: 10px; border-radius: 5px; border-left: 4px solid #667eea; }
        .message-box { background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #667eea; white-space: pre-wrap; }
        .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Contact Form Submission</h2>
        </div>
        <div class="content">
            <div class="field">
                <span class="label">Name</span>
                <div class="value">' . htmlspecialchars($contact['name']) . '</div>
            </div>
            <div class="field">
                <span class="label">Email</span>
                <div class="value">' . htmlspecialchars($contact['email']) . '</div>
            </div>
            <div class="field">
                <span class="label">Phone</span>
                <div class="value">' . htmlspecialchars($contact['phone'] ?: 'Not provided') . '</div>
            </div>
            <div class="field">
                <span class="label">Subject</span>
                <div class="value">' . htmlspecialchars($contact['subject']) . '</div>
            </div>
            <div class="field">
                <span class="label">Message</span>
                <div class="message-box">' . nl2br(htmlspecialchars($contact['message'])) . '</div>
            </div>
        </div>
        <div class="footer">
            <p>Sent via Travol Morocco contact form</p>
        </div>
    </div>
</body>
</html>';

        // Plain text version
        $plainBody = "New Contact Form Submission\n\n";
        $plainBody .= "Name: " . $contact['name'] . "\n";
        $plainBody .= "Email: " . $contact['email'] . "\n";
        $plainBody .= "Phone: " . ($contact['phone'] ?: 'Not provided') . "\n";
        $plainBody .= "Subject: " . $contact['subject'] . "\n";
        $plainBody .= "Message:\n" . $contact['message'] . "\n";

        $mail->isHTML(true);
        $mail->Subject = 'New Contact: ' . $contact['subject'];
        $mail->Body    = $htmlBody;
        $mail->AltBody = $plainBody;

        return $mail->send();
    } catch (Exception $e) {
        logError("Contact email failed: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Send booking confirmation email to admin and customer
 */
function sendBookingEmail($bookingData)
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mhamed.elouardani@gmail.com';
        $mail->Password   = 'icakemfkpqeiqkwv'; // no spaces
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('bookings@travolmorocco.com', 'Travol Morocco Bookings');
        $mail->addAddress('mhamed.elouardani@gmail.com'); // Admin
        $mail->addAddress($bookingData['customer']['email'], $bookingData['customer']['name']); // Customer
        $mail->addReplyTo('info@travolmorocco.com', 'Travol Morocco');

        // Build email content (your existing code is perfect – keep it)
        $itemsHtml = '';
        $itemsPlain = '';
        foreach ($bookingData['items'] as $item) {
            // Format people display based on pricing type
            $peopleDisplay = '';
            switch ($item['pricingType']) {
                case 'standard':
                    $peopleDisplay = "{$item['adults']} Adults, {$item['children']} Children";
                    break;
                case 'quad':
                    $peopleDisplay = "{$item['drivers']} Drivers, {$item['passengers']} Passengers";
                    break;
                case 'balloon':
                    $peopleDisplay = "{$item['people']} People ({$item['flightClass']} flight)";
                    break;
                case 'perBuggy':
                    $peopleDisplay = "{$item['buggies']} Buggies";
                    break;
                case 'perPerson':
                    $peopleDisplay = "{$item['persons']} Persons";
                    break;
                case 'scooter':
                    $peopleDisplay = "{$item['scooters']} Scooters ({$item['scooterOption']} tour)";
                    break;
                case 'adultChild':
                    $peopleDisplay = "{$item['adults']} Adults, {$item['children']} Children";
                    break;
                default:
                    $peopleDisplay = "{$item['adults']} Adults, {$item['children']} Children";
            }

            $itemsHtml .= "
            <tr>
                <td style='padding: 10px; border-bottom: 1px solid #ddd;'><strong>{$item['title']}</strong></td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$item['date']} at {$item['time']}</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$peopleDisplay}</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$item['totalPrice']} MAD</td>
            </tr>
            ";
            $itemsPlain .= "- {$item['title']}: {$item['date']} at {$item['time']}, {$peopleDisplay} - {$item['totalPrice']} MAD\n";
        }

        // HTML body (your existing full HTML structure – it's already complete)
        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                table { width: 100%; border-collapse: collapse; }
                th { background: #f0f0f0; padding: 10px; text-align: left; }
                td { padding: 10px; border-bottom: 1px solid #ddd; }
                .total { font-size: 18px; font-weight: bold; color: #28a745; text-align: right; margin-top: 20px; }
                .footer { margin-top: 30px; font-size: 12px; color: #999; text-align: center; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h2>Booking Confirmation</h2>
                <p>Booking ID: {$bookingData['booking_id']}</p>
            </div>
            <div class='content'>
                <p>Dear {$bookingData['customer']['name']},</p>
                <p>Thank you for booking with Travol Morocco! Your booking has been confirmed.</p>

                <h3>Booking Details</h3>
                <p><strong>Booking ID:</strong> {$bookingData['booking_id']}</p>
                <p><strong>Booking Date:</strong> {$bookingData['created_at']}</p>
                <p><strong>Payment Method:</strong> Cash on Arrival</p>
                <p><strong>Pickup Location:</strong> {$bookingData['customer']['pickup']}</p>

                <h3>Your Tours</h3>
                <table>
                    <thead>
                        <tr><th>Tour</th><th>Date/Time</th><th>Participants</th><th>Price</th></tr>
                    </thead>
                    <tbody>
                        {$itemsHtml}
                    </tbody>
                </table>

                <div class='total'>
                    Total Amount: {$bookingData['total']} MAD
                </div>

                <p><strong>What's next?</strong> Your guide will contact you on WhatsApp 24 hours before your tour. Please be at the pickup point 10 minutes before the scheduled time. Have cash ready for payment (MAD, EUR, or USD accepted).</p>

                <p>If you have any questions, please contact us on WhatsApp: +212 655 23 71 96.</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " Travol Morocco. All rights reserved.</p>
            </div>
        </body>
        </html>
        ";

        // Plain text version
        $plainBody = "Booking Confirmation\n\n";
        $plainBody .= "Booking ID: {$bookingData['booking_id']}\n";
        $plainBody .= "Booking Date: {$bookingData['created_at']}\n";
        $plainBody .= "Payment Method: Cash on Arrival\n";
        $plainBody .= "Pickup Location: {$bookingData['customer']['pickup']}\n\n";
        $plainBody .= "Your Tours:\n{$itemsPlain}\n";
        $plainBody .= "Total Amount: {$bookingData['total']} MAD\n\n";
        $plainBody .= "What's next? Your guide will contact you on WhatsApp 24 hours before your tour.\n";
        $plainBody .= "Contact us on WhatsApp: +212 655 23 71 96";

        $mail->isHTML(true);
        $mail->Subject = "Booking Confirmation - Travol Morocco (#{$bookingData['booking_id']})";
        $mail->Body = $htmlBody;
        $mail->AltBody = $plainBody;

        $mail->send();
        logMessage("Booking confirmation email sent for booking {$bookingData['booking_id']}", 'info');
        return true;
    } catch (Exception $e) {
        logError("Failed to send booking email: " . $mail->ErrorInfo, $bookingData);
        return false;
    }
}

/**
 * Send WhatsApp notification to admin (placeholder)
 */
function sendWhatsAppNotification($bookingData)
{
    $message = "🔔 *NEW BOOKING RECEIVED!*\n\n";
    $message .= "Booking ID: " . $bookingData['booking_id'] . "\n";
    $message .= "Customer: " . $bookingData['customer']['name'] . "\n";
    $message .= "Phone: " . $bookingData['customer']['phone'] . "\n";
    $message .= "Email: " . $bookingData['customer']['email'] . "\n";
    $message .= "Pickup: " . $bookingData['customer']['pickup'] . "\n";
    $message .= "Total: " . $bookingData['total'] . " MAD\n\n";
    $message .= "Tours:\n";

    foreach ($bookingData['items'] as $item) {
        $message .= "- " . $item['title'] . " (" . $item['date'] . " " . $item['time'] . ")\n";
    }

    logMessage("WhatsApp notification would be sent:\n" . $message, 'info');
    return true;
}
