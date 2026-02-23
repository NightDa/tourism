<?php
require_once '../includes/analytics.php';
require_once '../includes/config.php';

if ($_POST['book']) {
    $booking_data = [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'tour_id' => $_POST['tour_id'],
        'date' => $_POST['date'],
        'people' => $_POST['people'],
        'created_at' => date('Y-m-d H:i:s')
    ];

    // Save to JSON
    $bookingsFile = __DIR__ . '/../data/bookings.json';
    $bookings = json_decode(file_get_contents($bookingsFile), true);
    $bookings['bookings'][] = $booking_data;
    file_put_contents($bookingsFile, json_encode($bookings, JSON_PRETTY_PRINT));

    // Send email notification
    $to = "reservationrak@sti.ma";
    $subject = "New Booking Request";
    $message = "New booking from: " . $_POST['name'];
    mail($to, $subject, $message);
}
