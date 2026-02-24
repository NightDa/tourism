<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Send booking confirmation email to admin and customer
 */
function sendBookingEmail($bookingData)
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mhamed.elouardani@gmail.com'; // Your Gmail
        $mail->Password = 'icak emfk pqei qkwv'; // Your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('bookings@travolmorocco.com', 'Travol Morocco Bookings');
        $mail->addAddress('mhamed.elouardani@gmail.com'); // Admin email
        $mail->addAddress($bookingData['customer']['email'], $bookingData['customer']['name']); // Customer email
        $mail->addReplyTo('info@travolmorocco.com', 'Travol Morocco');

        // Build email content
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

        // HTML body
        $htmlBody = "
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
                        <tr>
                            <th>Tour</th>
                            <th>Date/Time</th>
                            <th>Participants</th>
                            <th>Price</th>
                        </tr>
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
        logMessage("Booking confirmation email sent to admin and customer for booking {$bookingData['booking_id']}", 'info');
        return true;
    } catch (Exception $e) {
        logError("Failed to send booking email: " . $mail->ErrorInfo, $bookingData);
        return false;
    }
}

/**
 * Send WhatsApp notification to admin (placeholder – replace with actual API)
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

    // Log the message (you can later replace with actual API call)
    logMessage("WhatsApp notification would be sent:\n" . $message, 'info');

    // For now, we'll just return true (simulate success)
    return true;
}
