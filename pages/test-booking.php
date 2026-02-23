<?php
// Simple test to check if process-booking.php is accessible
echo "Testing booking system...<br>";
echo "Current directory: " . __DIR__ . "<br>";

$bookingFile = __DIR__ . '/process-booking.php';
if (file_exists($bookingFile)) {
    echo "✅ process-booking.php exists<br>";
} else {
    echo "❌ process-booking.php NOT found!<br>";
}

$dataDir = __DIR__ . '/../data';
if (file_exists($dataDir)) {
    echo "✅ data folder exists<br>";
} else {
    echo "❌ data folder NOT found!<br>";
}

$bookingsFile = __DIR__ . '/../data/bookings.json';
if (file_exists($bookingsFile)) {
    echo "✅ bookings.json exists<br>";
    echo "File permissions: " . substr(sprintf('%o', fileperms($bookingsFile)), -4) . "<br>";
} else {
    echo "❌ bookings.json NOT found!<br>";
}

echo "<br><a href='javascript:void(0)' onclick='testAJAX()'>Test AJAX Booking</a>";
echo "<div id='result'></div>";

echo "<script>
function testAJAX() {
    var testData = {
        customer: {
            name: 'Test User',
            email: 'test@example.com',
            phone: '+212600000000',
            pickup: 'Test Location'
        },
        items: [{
            title: 'Test Tour',
            date: '2025-02-15',
            time: '09:00',
            adults: 2,
            children: 0,
            tourType: 'group',
            totalPrice: 700
        }],
        total: 700
    };
    
    fetch('process-booking.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'booking=' + encodeURIComponent(JSON.stringify(testData))
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('result').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
    })
    .catch(error => {
        document.getElementById('result').innerHTML = 'Error: ' + error;
    });
}
</script>";
