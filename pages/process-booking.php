<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header for JSON response
header('Content-Type: application/json');

// Get booking data
if (isset($_POST['booking'])) {
    $bookingData = json_decode($_POST['booking'], true);

    // Check if booking data is valid
    if (!$bookingData) {
        echo json_encode(['success' => false, 'error' => 'Invalid booking data']);
        exit;
    }

    // Add timestamp and booking ID
    $bookingData['created_at'] = date('Y-m-d H:i:s');
    $bookingData['booking_id'] = 'BK' . time() . rand(100, 999);

    // Save to bookings.json in data folder
    $bookingsFile = __DIR__ . '/../data/bookings.json';

    // Create data directory if it doesn't exist
    if (!file_exists(__DIR__ . '/../data')) {
        mkdir(__DIR__ . '/../data', 0777, true);
    }

    // Initialize bookings array
    $bookings = ['bookings' => []];

    // Read existing bookings if file exists
    if (file_exists($bookingsFile)) {
        $existingContent = file_get_contents($bookingsFile);
        if (!empty($existingContent)) {
            $bookings = json_decode($existingContent, true);
            if (!$bookings) {
                $bookings = ['bookings' => []];
            }
        }
    }

    // Add new booking
    $bookings['bookings'][] = $bookingData;

    // Save back to file
    $jsonOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
    if (file_put_contents($bookingsFile, json_encode($bookings, $jsonOptions))) {

        // Prepare email content (optional - comment out if you don't want emails)
        $to = "mhamed.elouardani@gmail.com";
        $subject = "New Booking - " . $bookingData['booking_id'];

        $message = "NEW BOOKING RECEIVED!\n\n";
        $message .= "==================================\n";
        $message .= "Booking ID: " . $bookingData['booking_id'] . "\n";
        $message .= "Date: " . $bookingData['created_at'] . "\n";
        $message .= "==================================\n\n";

        $message .= "CUSTOMER INFORMATION:\n";
        $message .= "----------------------\n";
        $message .= "Name: " . ($bookingData['customer']['name'] ?? 'N/A') . "\n";
        $message .= "Email: " . ($bookingData['customer']['email'] ?? 'N/A') . "\n";
        $message .= "Phone: " . ($bookingData['customer']['phone'] ?? 'N/A') . "\n";
        $message .= "Pickup: " . ($bookingData['customer']['pickup'] ?? 'N/A') . "\n\n";

        $message .= "BOOKED TOURS:\n";
        $message .= "--------------\n";
        if (isset($bookingData['items']) && is_array($bookingData['items'])) {
            foreach ($bookingData['items'] as $index => $item) {
                $message .= ($index + 1) . ". " . ($item['title'] ?? 'Unknown') . "\n";
                $message .= "   Date: " . ($item['date'] ?? 'N/A') . " at " . ($item['time'] ?? 'N/A') . "\n";
                $message .= "   " . ($item['adults'] ?? 0) . " Adults, " . ($item['children'] ?? 0) . " Children\n";
                $message .= "   " . ucfirst($item['tourType'] ?? 'group') . " Tour\n";
                $message .= "   Price: " . ($item['totalPrice'] ?? 0) . " MAD\n\n";
            }
        }

        $message .= "TOTAL AMOUNT: " . ($bookingData['total'] ?? 0) . " MAD\n";
        $message .= "Payment Method: Cash on Arrival\n";

        // Try to send email but don't fail if it doesn't work
        @mail($to, $subject, $message);

        // Return success response
        echo json_encode([
            'success' => true,
            'booking_id' => $bookingData['booking_id'],
            'message' => 'Booking confirmed!'
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to save booking']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No booking data received']);
}
