<?php
require_once 'config.php';
requireLogin();

$data = loadData();
$message = '';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    foreach ($data['excursions'] as $key => $excursion) {
        if ($excursion['id'] == $id) {
            unset($data['excursions'][$key]);
            break;
        }
    }
    $data['excursions'] = array_values($data['excursions']);
    saveData($data);
    $message = ['type' => 'success', 'text' => 'Excursion deleted successfully!'];
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        $excursion = [
            'id' => $id ?: getNextId($data),
            'title' => $_POST['title'],
            'category' => $_POST['category'],
            'image' => $_POST['image'],
            'duration' => $_POST['duration'],
            'location' => $_POST['location'],
            'description' => $_POST['description'],
            'priceTag' => $_POST['priceTag'],
            'groupPrice' => [
                'adult' => (int)$_POST['group_adult'],
                'child' => (int)$_POST['group_child']
            ],
            'privatePrice' => [
                'adult' => (int)$_POST['private_adult'],
                'child' => (int)$_POST['private_child']
            ]
        ];

        if ($_POST['action'] === 'add') {
            $data['excursions'][] = $excursion;
            $message = ['type' => 'success', 'text' => 'Excursion added successfully!'];
        } elseif ($_POST['action'] === 'edit') {
            foreach ($data['excursions'] as $key => $e) {
                if ($e['id'] == $id) {
                    $data['excursions'][$key] = $excursion;
                    break;
                }
            }
            $message = ['type' => 'success', 'text' => 'Excursion updated successfully!'];
        }

        saveData($data);
    }
}

// Get excursion for editing
$editExcursion = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    foreach ($data['excursions'] as $e) {
        if ($e['id'] == $editId) {
            $editExcursion = $e;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Travol Morocco</title>
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
            flex-wrap: wrap;
            gap: 15px;
        }

        .navbar h1 {
            font-size: 24px;
        }

        .navbar h1 i {
            margin-right: 10px;
        }

        /* Navigation Links - FIXED */
        .nav-links {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
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

        .user-info span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
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

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 40px;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }

        .stat-card .label {
            color: #666;
            margin-top: 5px;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .card-header h2 {
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h2 i {
            margin-right: 10px;
        }

        .card-body {
            padding: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #e1e1e1;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .price-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .price-section h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .price-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-edit {
            background: #ffc107;
            color: #333;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-view {
            background: #17a2b8;
            color: white;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            white-space: nowrap;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #e1e1e1;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .category-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .category-cultural {
            background: #e3f2fd;
            color: #1976d2;
        }

        .category-mountain {
            background: #e8f5e8;
            color: #388e3c;
        }

        .category-desert {
            background: #fff3e0;
            color: #f57c00;
        }

        .category-coastal {
            background: #e0f2f1;
            color: #00796b;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
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

        .assignment-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            background: #e3f2fd;
            color: #1976d2;
            white-space: nowrap;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .action-buttons .btn {
            padding: 5px 10px;
            font-size: 12px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links {
                width: 100%;
                justify-content: flex-start;
            }

            .user-info {
                width: 100%;
                justify-content: space-between;
                margin-top: 10px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full-width {
                grid-column: span 1;
            }

            .price-row {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            table {
                font-size: 14px;
            }

            td,
            th {
                padding: 8px;
            }

            .action-buttons {
                flex-direction: column;
            }
        }

        /* Utility classes */
        small {
            color: #666;
            font-size: 12px;
            display: block;
            margin-top: 5px;
        }

        .text-center {
            text-align: center;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <h1><i class="fas fa-cog"></i> Travol Morocco Admin</h1>
        <div class="nav-links">
            <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Dashboard
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
    </nav>
    <div class="container">
        <?php if ($message): ?>
            <div class="message <?php echo $message['type']; ?>">
                <i class="fas <?php echo $message['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <?php echo $message['text']; ?>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-map-marked-alt"></i>
                <div class="number"><?php echo count($data['excursions']); ?></div>
                <div class="label">Total Excursions</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-tags"></i>
                <div class="number">
                    <?php
                    $categories = array_unique(array_column($data['excursions'], 'category'));
                    echo count($categories);
                    ?>
                </div>
                <div class="label">Categories</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <div class="number">
                    <?php
                    echo count(array_filter($data['excursions'], function ($e) {
                        return strpos($e['duration'], 'Full') !== false;
                    }));
                    ?>
                </div>
                <div class="label">Full Day Tours</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-sync-alt"></i>
                <div class="number">
                    <?php
                    $lastUpdated = isset($data['last_updated']) ? date('H:i', strtotime($data['last_updated'])) : 'Now';
                    echo $lastUpdated;
                    ?>
                </div>
                <div class="label">Last Updated</div>
            </div>
        </div>

        <!-- Add/Edit Form -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas <?php echo $editExcursion ? 'fa-edit' : 'fa-plus-circle'; ?>"></i>
                    <?php echo $editExcursion ? 'Edit Excursion' : 'Add New Excursion'; ?>
                </h2>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="<?php echo $editExcursion ? 'edit' : 'add'; ?>">
                    <?php if ($editExcursion): ?>
                        <input type="hidden" name="id" value="<?php echo $editExcursion['id']; ?>">
                    <?php endif; ?>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Title *</label>
                            <input type="text" name="title" required value="<?php echo $editExcursion['title'] ?? ''; ?>">
                        </div>

                        <div class="form-group">
                            <label>Category *</label>
                            <select name="category" required>
                                <option value="cultural" <?php echo ($editExcursion['category'] ?? '') === 'cultural' ? 'selected' : ''; ?>>Cultural</option>
                                <option value="mountain" <?php echo ($editExcursion['category'] ?? '') === 'mountain' ? 'selected' : ''; ?>>Mountain</option>
                                <option value="desert" <?php echo ($editExcursion['category'] ?? '') === 'desert' ? 'selected' : ''; ?>>Desert</option>
                                <option value="coastal" <?php echo ($editExcursion['category'] ?? '') === 'coastal' ? 'selected' : ''; ?>>Coastal</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label>Image URL *</label>
                            <input type="url" name="image" required value="<?php echo $editExcursion['image'] ?? ''; ?>">
                        </div>

                        <div class="form-group">
                            <label>Duration *</label>
                            <input type="text" name="duration" required value="<?php echo $editExcursion['duration'] ?? 'Full Day'; ?>">
                        </div>

                        <div class="form-group">
                            <label>Location *</label>
                            <input type="text" name="location" required value="<?php echo $editExcursion['location'] ?? 'Marrakech'; ?>">
                        </div>

                        <div class="form-group full-width">
                            <label>Description *</label>
                            <textarea name="description" rows="3" required><?php echo $editExcursion['description'] ?? ''; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Price Tag Display *</label>
                            <input type="text" name="priceTag" required value="<?php echo $editExcursion['priceTag'] ?? 'From 350 MAD'; ?>">
                        </div>
                    </div>

                    <div class="price-section">
                        <h3>Group Tour Prices (MAD)</h3>
                        <div class="price-row">
                            <div class="form-group">
                                <label>Adult Price</label>
                                <input type="number" name="group_adult" required value="<?php echo $editExcursion['groupPrice']['adult'] ?? 350; ?>">
                            </div>
                            <div class="form-group">
                                <label>Child Price</label>
                                <input type="number" name="group_child" required value="<?php echo $editExcursion['groupPrice']['child'] ?? 175; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="price-section">
                        <h3>Private Tour Prices (MAD)</h3>
                        <div class="price-row">
                            <div class="form-group">
                                <label>Adult Price</label>
                                <input type="number" name="private_adult" required value="<?php echo $editExcursion['privatePrice']['adult'] ?? 650; ?>">
                            </div>
                            <div class="form-group">
                                <label>Child Price</label>
                                <input type="number" name="private_child" required value="<?php echo $editExcursion['privatePrice']['child'] ?? 325; ?>">
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $editExcursion ? 'Update Excursion' : 'Save Excursion'; ?>
                        </button>
                        <?php if ($editExcursion): ?>
                            <a href="index.php" class="btn" style="background: #6c757d; color: white;">
                                <i class="fas fa-times"></i> Cancel Edit
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
                <!-- In the form, add file upload -->
                <div class="form-group">
                    <label>Upload Image</label>
                    <input type="file" name="image_file" accept="image/*">
                    <small>Or enter image URL above</small>
                </div>
            </div>
        </div>

        <!-- Excursions List -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-list"></i> All Excursions</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Location</th>
                                <th>Duration</th>
                                <th>Group Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['excursions'] as $excursion): ?>
                                <tr>
                                    <td>#<?php echo $excursion['id']; ?></td>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($excursion['image']); ?>"
                                            alt="<?php echo htmlspecialchars($excursion['title']); ?>"
                                            style="width: 50px; height: 40px; object-fit: cover; border-radius: 4px;">
                                    </td>
                                    <td><?php echo htmlspecialchars($excursion['title']); ?></td>
                                    <td>
                                        <span class="category-badge category-<?php echo $excursion['category']; ?>">
                                            <?php echo ucfirst($excursion['category']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($excursion['location']); ?></td>
                                    <td><?php echo htmlspecialchars($excursion['duration']); ?></td>
                                    <td><?php echo $excursion['priceTag']; ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="?edit=<?php echo $excursion['id']; ?>" class="btn btn-edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?delete=<?php echo $excursion['id']; ?>"
                                                class="btn btn-delete"
                                                onclick="return confirm('Are you sure you want to delete this excursion?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <a href="../excursions.html#<?php echo strtolower(str_replace(' ', '-', $excursion['title'])); ?>"
                                                target="_blank"
                                                class="btn btn-view">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($data['excursions'])): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 40px;">
                                        <i class="fas fa-info-circle" style="font-size: 48px; color: #ccc; margin-bottom: 10px;"></i>
                                        <p>No excursions found. Add your first excursion above!</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Recent Bookings Section -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-calendar-check"></i> Recent Bookings</h2>
                <a href="bookings.php" class="btn btn-primary btn-sm">View All</a>
            </div>
            <div class="card-body">
                <?php
                $bookingsFile = __DIR__ . '/../data/bookings.json';
                if (file_exists($bookingsFile)) {
                    $bookings = json_decode(file_get_contents($bookingsFile), true);
                    $recentBookings = array_slice($bookings['bookings'] ?? [], 0, 5);

                    if (!empty($recentBookings)):
                ?>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; min-width: 800px;">
                                <thead>
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Customer</th>
                                        <th>Contact</th>
                                        <th>Tours</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Assigned</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentBookings as $booking):
                                        $status = $booking['status'] ?? 'pending';
                                        $assignedTo = $booking['assigned_to'] ?? '';
                                    ?>
                                        <tr>
                                            <td><strong><?php echo $booking['booking_id']; ?></strong></td>
                                            <td>
                                                <?php echo htmlspecialchars($booking['customer']['name']); ?><br>
                                                <small><?php echo htmlspecialchars($booking['customer']['email']); ?></small>
                                            </td>
                                            <td>
                                                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $booking['customer']['phone']); ?>" target="_blank">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                                <a href="mailto:<?php echo $booking['customer']['email']; ?>">
                                                    <i class="fas fa-envelope"></i>
                                                </a>
                                                <small><?php echo htmlspecialchars($booking['customer']['phone']); ?></small>
                                            </td>
                                            <td>
                                                <?php echo count($booking['items']); ?> tour(s)<br>
                                                <small>
                                                    <?php
                                                    $firstItem = $booking['items'][0] ?? null;
                                                    if ($firstItem) {
                                                        echo $firstItem['date'] . ' ' . $firstItem['time'];
                                                    }
                                                    ?>
                                                </small>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($booking['created_at'])); ?></td>
                                            <td><strong><?php echo $booking['total']; ?> MAD</strong></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $status; ?>">
                                                    <?php echo ucfirst($status); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($assignedTo): ?>
                                                    <span class="assignment-badge"><?php echo ucfirst($assignedTo); ?></span>
                                                <?php else: ?>
                                                    <span style="color: #999;">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="bookings.php?view=<?php echo $booking['booking_id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p style="text-align: center; padding: 30px;">No bookings yet.</p>
                <?php
                    endif;
                } else {
                    echo '<p style="text-align: center; padding: 30px;">Bookings file not found.</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> Travol Morocco Admin Panel. All rights reserved.</p>
    </div>
</body>

</html>