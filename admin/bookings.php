<?php
require_once 'config.php';
requireLogin();

// Load bookings
$bookingsFile = __DIR__ . '/../data/bookings.json';
$hotelsFile = __DIR__ . '/../data/hotels.json';

$bookings = ['bookings' => []];
if (file_exists($bookingsFile)) {
    $bookings = json_decode(file_get_contents($bookingsFile), true);
}

// Load hotels for reference
$hotels = [];
if (file_exists($hotelsFile)) {
    $hotelsData = json_decode(file_get_contents($hotelsFile), true);
    // Flatten hotels list for quick lookup
    foreach ($hotelsData['zones'] as $zone) {
        foreach ($zone['hotels'] as $hotel) {
            $hotels[$hotel['name']] = $hotel;
        }
    }
}

// Handle booking status update
if (isset($_POST['update_status'])) {
    $bookingId = $_POST['booking_id'];
    $newStatus = $_POST['status'];
    $assignedTo = $_POST['assigned_to'] ?? '';

    foreach ($bookings['bookings'] as &$booking) {
        if ($booking['booking_id'] == $bookingId) {
            $booking['status'] = $newStatus;
            $booking['assigned_to'] = $assignedTo;
            $booking['updated_at'] = date('Y-m-d H:i:s');
            break;
        }
    }

    file_put_contents($bookingsFile, json_encode($bookings, JSON_PRETTY_PRINT));

    // Send notification if status changed to confirmed
    if ($newStatus == 'confirmed' && isset($_POST['notify_customer'])) {
        sendCustomerNotification($booking);
    }

    header('Location: bookings.php?updated=1');
    exit;
}

// Handle delete booking
if (isset($_GET['delete'])) {
    $bookingId = $_GET['delete'];
    $bookings['bookings'] = array_filter($bookings['bookings'], function ($b) use ($bookingId) {
        return $b['booking_id'] != $bookingId;
    });
    $bookings['bookings'] = array_values($bookings['bookings']);
    file_put_contents($bookingsFile, json_encode($bookings, JSON_PRETTY_PRINT));
    header('Location: bookings.php?deleted=1');
    exit;
}

// Function to send customer notification
function sendCustomerNotification($booking)
{
    $to = $booking['customer']['email'];
    $subject = "Your Booking is Confirmed - Travol Morocco";

    $message = "Dear " . $booking['customer']['name'] . ",\n\n";
    $message .= "Great news! Your booking has been confirmed.\n\n";
    $message .= "Booking ID: " . $booking['booking_id'] . "\n";
    $message .= "Total Amount: " . $booking['total'] . " MAD\n";
    $message .= "Payment: Cash on arrival\n\n";

    $message .= "BOOKED TOURS:\n";
    foreach ($booking['items'] as $item) {
        $message .= "- " . $item['title'] . "\n";
        $message .= "  Date: " . $item['date'] . " at " . $item['time'] . "\n";
        $message .= "  " . $item['adults'] . " Adults, " . $item['children'] . " Children\n";
        $message .= "  " . ucfirst($item['tourType']) . " Tour\n\n";
    }

    $message .= "PICKUP LOCATION: " . $booking['customer']['pickup'] . "\n\n";
    $message .= "Your guide will contact you on WhatsApp shortly.\n";
    $message .= "Emergency contact: +212 524 43 34 51\n\n";
    $message .= "Thank you for choosing Travol Morocco!\n";

    $headers = "From: bookings@travolmorocco.com\r\n";
    @mail($to, $subject, $message, $headers);

    // Also send WhatsApp message (you'll need to integrate with WhatsApp API)
    $whatsappMessage = "Your booking is confirmed! Booking ID: " . $booking['booking_id'];
    // whatsappIntegration($booking['customer']['phone'], $whatsappMessage);
}

// Get counts by status
$totalBookings = count($bookings['bookings']);
$pendingCount = count(array_filter($bookings['bookings'], function ($b) {
    return ($b['status'] ?? 'pending') == 'pending';
}));
$confirmedCount = count(array_filter($bookings['bookings'], function ($b) {
    return ($b['status'] ?? '') == 'confirmed';
}));
$completedCount = count(array_filter($bookings['bookings'], function ($b) {
    return ($b['status'] ?? '') == 'completed';
}));
$cancelledCount = count(array_filter($bookings['bookings'], function ($b) {
    return ($b['status'] ?? '') == 'cancelled';
}));

// Sort bookings by date (newest first)
usort($bookings['bookings'], function ($a, $b) {
    return strtotime($b['created_at'] ?? '') - strtotime($a['created_at'] ?? '');
});
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Travol Morocco Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar h1 {
            font-size: 24px;
        }

        .navbar h1 i {
            margin-right: 10px;
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            background: rgba(255, 255, 255, 0.2);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
        }

        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 30px;
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card.pending {
            border-left: 4px solid #ffc107;
        }

        .stat-card.confirmed {
            border-left: 4px solid #28a745;
        }

        .stat-card.completed {
            border-left: 4px solid #17a2b8;
        }

        .stat-card.cancelled {
            border-left: 4px solid #dc3545;
        }

        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }

        .stat-card .label {
            color: #666;
        }

        .filters {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filters input,
        .filters select {
            padding: 10px 15px;
            border: 2px solid #e1e1e1;
            border-radius: 6px;
            font-size: 14px;
            flex: 1;
            min-width: 200px;
        }

        .filters button {
            padding: 10px 25px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .bookings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
        }

        .booking-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s;
        }

        .booking-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .booking-header {
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
        }

        .booking-id {
            font-weight: bold;
            color: #667eea;
        }

        .booking-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-completed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .booking-body {
            padding: 20px;
        }

        .customer-info {
            margin-bottom: 15px;
        }

        .customer-info p {
            margin: 5px 0;
            color: #333;
        }

        .customer-info i {
            width: 25px;
            color: #667eea;
        }

        .tour-items {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }

        .tour-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .tour-item:last-child {
            border-bottom: none;
        }

        .tour-title {
            font-weight: 600;
            color: #333;
        }

        .tour-details {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .tour-details span {
            background: white;
            padding: 3px 8px;
            border-radius: 4px;
        }

        .booking-footer {
            padding: 15px 20px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-price {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }

        .booking-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-warning {
            background: #ffc107;
            color: #333;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-info {
            background: #17a2b8;
            color: white;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background: white;
            margin: 50px auto;
            padding: 30px;
            width: 90%;
            max-width: 600px;
            border-radius: 10px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .close {
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group select,
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 2px solid #e1e1e1;
            border-radius: 6px;
        }

        .assignment-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            background: #e3f2fd;
            color: #1976d2;
        }

        @media (max-width: 768px) {
            .bookings-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="navbar">
        <h1><i class="fas fa-calendar-check"></i> Manage Bookings</h1>
        <div class="nav-links">
            <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="packages.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'packages.php' ? 'active' : ''; ?>">
                <i class="fas fa-box"></i> Packages
            </a>
            <a href="destinations.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'destinations.php' ? 'active' : ''; ?>">
                <i class="fas fa-map-marked-alt"></i> Destinations
            </a>
            <a href="bookings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check"></i> Bookings
            </a>
            <a href="analytics.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i> Analytics
            </a>
        </div>
        <div class="user-info">
            <span><i class="fas fa-user"></i> <?php echo $_SESSION['admin_username']; ?></span>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="container">
        <?php if (isset($_GET['updated'])): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i> Booking updated successfully!
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i> Booking deleted successfully!
            </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card" onclick="filterBookings('all')">
                <i class="fas fa-calendar-alt"></i>
                <div class="number"><?php echo $totalBookings; ?></div>
                <div class="label">Total Bookings</div>
            </div>
            <div class="stat-card pending" onclick="filterBookings('pending')">
                <i class="fas fa-clock"></i>
                <div class="number"><?php echo $pendingCount; ?></div>
                <div class="label">Pending</div>
            </div>
            <div class="stat-card confirmed" onclick="filterBookings('confirmed')">
                <i class="fas fa-check-circle"></i>
                <div class="number"><?php echo $confirmedCount; ?></div>
                <div class="label">Confirmed</div>
            </div>
            <div class="stat-card completed" onclick="filterBookings('completed')">
                <i class="fas fa-check-double"></i>
                <div class="number"><?php echo $completedCount; ?></div>
                <div class="label">Completed</div>
            </div>
            <div class="stat-card cancelled" onclick="filterBookings('cancelled')">
                <i class="fas fa-times-circle"></i>
                <div class="number"><?php echo $cancelledCount; ?></div>
                <div class="label">Cancelled</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <input type="text" id="searchInput" placeholder="Search by name, email, or booking ID...">
            <select id="statusFilter">
                <option value="all">All Status</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <select id="assigneeFilter">
                <option value="all">All Assignees</option>
                <option value="khalid">Khalid</option>
                <option value="rabii">Rabii</option>
                <option value="unassigned">Unassigned</option>
            </select>
            <button onclick="applyFilters()"><i class="fas fa-search"></i> Apply Filters</button>
        </div>

        <!-- Bookings Grid -->
        <div class="bookings-grid" id="bookingsGrid">
            <?php foreach ($bookings['bookings'] as $booking):
                $status = $booking['status'] ?? 'pending';
                $assignedTo = $booking['assigned_to'] ?? '';
            ?>
                <div class="booking-card" data-status="<?php echo $status; ?>" data-assignee="<?php echo $assignedTo; ?>" data-search="<?php echo strtolower($booking['customer']['name'] . ' ' . $booking['customer']['email'] . ' ' . $booking['booking_id']); ?>">
                    <div class="booking-header">
                        <span class="booking-id"><i class="fas fa-hashtag"></i> <?php echo $booking['booking_id']; ?></span>
                        <span class="booking-status status-<?php echo $status; ?>">
                            <?php echo ucfirst($status); ?>
                        </span>
                    </div>

                    <div class="booking-body">
                        <div class="customer-info">
                            <p><i class="fas fa-user"></i> <?php echo htmlspecialchars($booking['customer']['name']); ?></p>
                            <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($booking['customer']['email']); ?></p>
                            <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($booking['customer']['phone']); ?></p>
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($booking['customer']['pickup'] ?? 'Not specified'); ?></p>
                            <?php if ($assignedTo): ?>
                                <p><i class="fas fa-user-tie"></i> Assigned to: <span class="assignment-badge"><?php echo ucfirst($assignedTo); ?></span></p>
                            <?php endif; ?>
                        </div>

                        <div class="tour-items">
                            <h4><i class="fas fa-suitcase"></i> Booked Tours (<?php echo count($booking['items']); ?>)</h4>
                            <?php foreach ($booking['items'] as $item): ?>
                                <div class="tour-item">
                                    <div class="tour-title"><?php echo htmlspecialchars($item['title']); ?></div>
                                    <div class="tour-details">
                                        <span><i class="fas fa-calendar"></i> <?php echo $item['date']; ?></span>
                                        <span><i class="fas fa-clock"></i> <?php echo $item['time']; ?></span>
                                        <span><i class="fas fa-user"></i> <?php echo $item['adults']; ?>A</span>
                                        <?php if ($item['children'] > 0): ?>
                                            <span><i class="fas fa-child"></i> <?php echo $item['children']; ?>C</span>
                                        <?php endif; ?>
                                        <span><i class="fas fa-users"></i> <?php echo ucfirst($item['tourType']); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="booking-footer">
                            <div>
                                <small><i class="fas fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($booking['created_at'])); ?></small>
                            </div>
                            <div class="total-price">
                                <?php echo $booking['total']; ?> MAD
                            </div>
                        </div>

                        <div class="booking-actions" style="margin-top: 15px;">
                            <button class="btn btn-primary btn-sm" onclick="viewBookingDetails(<?php echo htmlspecialchars(json_encode($booking)); ?>)">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="editBooking('<?php echo $booking['booking_id']; ?>', '<?php echo $status; ?>', '<?php echo $assignedTo; ?>')">
                                <i class="fas fa-edit"></i> Update
                            </button>
                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $booking['customer']['phone']); ?>" target="_blank" class="btn btn-success btn-sm">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                            <a href="mailto:<?php echo $booking['customer']['email']; ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-envelope"></i> Email
                            </a>
                            <button class="btn btn-danger btn-sm" onclick="deleteBooking('<?php echo $booking['booking_id']; ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($bookings['bookings'])): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 60px;">
                    <i class="fas fa-calendar-times" style="font-size: 48px; color: #ccc;"></i>
                    <h3>No Bookings Yet</h3>
                    <p>When customers book tours, they will appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Booking Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Update Booking</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>

            <form method="POST" action="">
                <input type="hidden" name="booking_id" id="edit_booking_id">

                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Status</label>
                    <select name="status" id="edit_status" class="form-control">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-user-tie"></i> Assign to</label>
                    <select name="assigned_to" id="edit_assigned" class="form-control">
                        <option value="">Select guide...</option>
                        <option value="khalid">Khalid</option>
                        <option value="rabii">Rabii</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="notify_customer" value="1" checked>
                        <i class="fas fa-bell"></i> Send confirmation email to customer
                    </label>
                </div>

                <button type="submit" name="update_status" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-save"></i> Update Booking
                </button>
            </form>
        </div>
    </div>

    <!-- View Details Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h2><i class="fas fa-file-invoice"></i> Booking Details</h2>
                <span class="close" onclick="closeViewModal()">&times;</span>
            </div>
            <div id="bookingDetails"></div>
        </div>
    </div>

    <script>
        // Filter bookings
        function filterBookings(status) {
            document.getElementById('statusFilter').value = status;
            applyFilters();
        }

        function applyFilters() {
            const status = document.getElementById('statusFilter').value;
            const assignee = document.getElementById('assigneeFilter').value;
            const search = document.getElementById('searchInput').value.toLowerCase();

            const cards = document.querySelectorAll('.booking-card');

            cards.forEach(card => {
                const cardStatus = card.dataset.status;
                const cardAssignee = card.dataset.assignee || 'unassigned';
                const cardSearch = card.dataset.search;

                let statusMatch = status === 'all' || cardStatus === status;
                let assigneeMatch = assignee === 'all' ||
                    (assignee === 'unassigned' && !card.dataset.assignee) ||
                    cardAssignee === assignee;
                let searchMatch = search === '' || cardSearch.includes(search);

                if (statusMatch && assigneeMatch && searchMatch) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Edit booking
        function editBooking(id, status, assigned) {
            document.getElementById('edit_booking_id').value = id;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_assigned').value = assigned || '';
            document.getElementById('editModal').style.display = 'block';
        }

        // View booking details
        function viewBookingDetails(booking) {
            let html = '<div style="padding: 20px;">';

            html += '<h3>Customer Information</h3>';
            html += '<p><strong>Name:</strong> ' + booking.customer.name + '</p>';
            html += '<p><strong>Email:</strong> ' + booking.customer.email + '</p>';
            html += '<p><strong>Phone:</strong> ' + booking.customer.phone + '</p>';
            html += '<p><strong>Pickup:</strong> ' + (booking.customer.pickup || 'Not specified') + '</p>';

            html += '<h3 style="margin-top: 20px;">Booked Tours</h3>';
            booking.items.forEach((item, index) => {
                html += '<div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 10px;">';
                html += '<p><strong>' + item.title + '</strong></p>';
                html += '<p>Date: ' + item.date + ' at ' + item.time + '</p>';
                html += '<p>Adults: ' + item.adults + ', Children: ' + item.children + '</p>';
                html += '<p>Tour Type: ' + item.tourType + '</p>';
                html += '<p>Price: ' + item.totalPrice + ' MAD</p>';
                if (item.specialRequests) {
                    html += '<p><em>Special Requests: ' + item.specialRequests + '</em></p>';
                }
                html += '</div>';
            });

            html += '<h3 style="margin-top: 20px;">Payment Summary</h3>';
            html += '<p><strong>Total:</strong> ' + booking.total + ' MAD</p>';
            html += '<p><strong>Payment Method:</strong> Cash on Arrival</p>';
            html += '<p><strong>Booking Date:</strong> ' + new Date(booking.created_at).toLocaleString() + '</p>';
            html += '<p><strong>Status:</strong> ' + (booking.status || 'pending') + '</p>';
            if (booking.assigned_to) {
                html += '<p><strong>Assigned to:</strong> ' + booking.assigned_to + '</p>';
            }

            html += '</div>';

            document.getElementById('bookingDetails').innerHTML = html;
            document.getElementById('viewModal').style.display = 'block';
        }

        // Delete booking
        function deleteBooking(id) {
            if (confirm('Are you sure you want to delete this booking? This action cannot be undone.')) {
                window.location.href = 'bookings.php?delete=' + id;
            }
        }

        // Close modals
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const editModal = document.getElementById('editModal');
            const viewModal = document.getElementById('viewModal');
            if (event.target == editModal) {
                editModal.style.display = 'none';
            }
            if (event.target == viewModal) {
                viewModal.style.display = 'none';
            }
        }

        // Search on input
        document.getElementById('searchInput').addEventListener('input', applyFilters);
        document.getElementById('statusFilter').addEventListener('change', applyFilters);
        document.getElementById('assigneeFilter').addEventListener('change', applyFilters);
    </script>
</body>

</html>