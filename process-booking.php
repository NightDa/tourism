<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header for JSON response
header('Content-Type: application/json');

// Get booking data
if (isset($_POST['booking'])) {
    $bookingData = json_decode($_POST['booking'], true);

    // Add timestamp and booking ID
    $bookingData['created_at'] = date('Y-m-d H:i:s');
    $bookingData['booking_id'] = 'BK' . time() . rand(100, 999);

    // Save to bookings.json in data folder
    $bookingsFile = __DIR__ . '/data/bookings.json';

    // Create data directory if it doesn't exist
    if (!file_exists(__DIR__ . '/data')) {
        mkdir(__DIR__ . '/data', 0777, true);
    }

    // Create file if it doesn't exist
    if (!file_exists($bookingsFile)) {
        $bookings = ['bookings' => []];
        file_put_contents($bookingsFile, json_encode($bookings, JSON_PRETTY_PRINT));
    }

    // Read existing bookings
    $bookings = json_decode(file_get_contents($bookingsFile), true);

    // Add new booking
    $bookings['bookings'][] = $bookingData;

    // Save back to file
    file_put_contents($bookingsFile, json_encode($bookings, JSON_PRETTY_PRINT));

    // Prepare email content
    $to = "mhamed.elouardani@gmail.com";
    $subject = "New Booking - " . $bookingData['booking_id'];

    $message = "NEW BOOKING RECEIVED!\n\n";
    $message .= "==================================\n";
    $message .= "Booking ID: " . $bookingData['booking_id'] . "\n";
    $message .= "Date: " . $bookingData['created_at'] . "\n";
    $message .= "==================================\n\n";

    $message .= "CUSTOMER INFORMATION:\n";
    $message .= "----------------------\n";
    $message .= "Name: " . $bookingData['customer']['name'] . "\n";
    $message .= "Email: " . $bookingData['customer']['email'] . "\n";
    $message .= "Phone: " . $bookingData['customer']['phone'] . "\n";
    $message .= "Pickup: " . ($bookingData['customer']['pickup'] ?? 'Your Marrakech Riad/Hotel') . "\n\n";

    $message .= "BOOKED TOURS:\n";
    $message .= "--------------\n";
    foreach ($bookingData['items'] as $index => $item) {
        $message .= ($index + 1) . ". " . $item['title'] . "\n";
        $message .= "   Date: " . $item['date'] . " at " . $item['time'] . "\n";
        $message .= "   " . $item['adults'] . " Adults, " . $item['children'] . " Children\n";
        $message .= "   " . ucfirst($item['tourType']) . " Tour\n";
        $message .= "   Price: " . $item['totalPrice'] . " MAD\n";
        if (!empty($item['specialRequests'])) {
            $message .= "   Special Requests: " . $item['specialRequests'] . "\n";
        }
        $message .= "\n";
    }

    $message .= "PAYMENT SUMMARY:\n";
    $message .= "----------------\n";
    $message .= "Total Amount: " . $bookingData['total'] . " MAD\n";
    $message .= "Payment Method: Cash on Arrival\n\n";

    $message .= "QUICK ACTIONS:\n";
    $message .= "--------------\n";
    $message .= "WhatsApp: https://wa.me/" . str_replace(['+', ' '], '', $bookingData['customer']['phone']) . "\n";
    $message .= "Email: mailto:" . $bookingData['customer']['email'] . "\n\n";

    $message .= "Admin Dashboard: http://localhost:8000/admin/index.php\n";

    $headers = "From: bookings@travolmorocco.com\r\n";
    $headers .= "Reply-To: " . $bookingData['customer']['email'] . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send email
    mail($to, $subject, $message, $headers);

    // Also send a copy to customer (optional)
    $customer_subject = "Your Booking Confirmation - Travol Morocco";
    $customer_message = "Dear " . $bookingData['customer']['name'] . ",\n\n";
    $customer_message .= "Thank you for booking with Travol Morocco! We have received your booking request.\n\n";
    $customer_message .= "Booking ID: " . $bookingData['booking_id'] . "\n";
    $customer_message .= "Total Amount: " . $bookingData['total'] . " MAD\n";
    $customer_message .= "Payment: Cash on arrival\n\n";
    $customer_message .= "We will contact you shortly on WhatsApp to confirm your booking.\n\n";
    $customer_message .= "Best regards,\nTravol Morocco Team\n";
    $customer_message .= "Phone: +212 524 43 34 51";

    $customer_headers = "From: reservations@travolmorocco.com\r\n";
    mail($bookingData['customer']['email'], $customer_subject, $customer_message, $customer_headers);

    // Return success response
    echo json_encode([
        'success' => true,
        'booking_id' => $bookingData['booking_id'],
        'message' => 'Booking confirmed!'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'No booking data received'
    ]);
}
