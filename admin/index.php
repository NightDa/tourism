<?php
require_once 'config.php';
requireLogin();

$data = loadData();
$message = '';
$pricingTypes = getPricingTypes();
$categories = getCategories();

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
    header('Location: index.php?deleted=1');
    exit;
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $pricingType = $_POST['pricing_type'];

        // Base excursion data
        $excursion = [
            'id' => $id ?: getNextId($data),
            'title' => $_POST['title'],
            'category' => $_POST['category'],
            'image' => $_POST['image'],
            'duration' => $_POST['duration'],
            'location' => $_POST['location'],
            'description' => $_POST['description'],
            'priceTag' => $_POST['priceTag'],
            'pricingType' => $pricingType
        ];

        // Add pricing based on type (only what's needed)
        switch ($pricingType) {
            case 'standard':
                $excursion['groupPrice'] = [
                    'adult' => (int)$_POST['group_adult'],
                    'child' => (int)$_POST['group_child']
                ];
                $excursion['privatePrice'] = [
                    'adult' => (int)$_POST['private_adult'],
                    'child' => (int)$_POST['private_child']
                ];
                break;

            case 'quad':
                $excursion['groupPrice'] = [
                    'adult' => (int)$_POST['driver_price'],
                    'child' => (int)$_POST['passenger_price']
                ];
                break;

            case 'balloon':
                $excursion['flightClasses'] = [
                    'classic' => (int)$_POST['classic_price'],
                    'premium' => (int)$_POST['premium_price'],
                    'vip' => (int)$_POST['vip_price'],
                    'royal' => (int)$_POST['royal_price']
                ];
                break;

            case 'perBuggy':
                $excursion['pricePerBuggy'] = (int)$_POST['price_per_buggy'];
                break;

            case 'perPerson':
                $excursion['pricePerPerson'] = (int)$_POST['price_per_person'];
                break;

            case 'scooter':
                // Fixed pricing - no input needed
                break;

            case 'adultChild':
                $excursion['prices'] = [
                    'adult' => (int)$_POST['adult_price'],
                    'child' => (int)$_POST['child_price']
                ];
                break;
        }

        if ($_POST['action'] === 'add') {
            $data['excursions'][] = $excursion;
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Excursion added successfully!'];
        } elseif ($_POST['action'] === 'edit') {
            foreach ($data['excursions'] as $key => $e) {
                if ($e['id'] == $id) {
                    $data['excursions'][$key] = $excursion;
                    break;
                }
            }
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Excursion updated successfully!'];
        }

        saveData($data);
        header('Location: index.php');
        exit;
    }
}

// Get message from session
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
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
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
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

        .pricing-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            border-left: 4px solid #667eea;
        }

        .pricing-card h3 {
            margin-bottom: 15px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pricing-card h3 i {
            color: #667eea;
        }

        .pricing-card .info-text {
            background: #e3f2fd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #1976d2;
        }

        .pricing-card .info-text i {
            margin-right: 5px;
        }

        .price-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .price-row-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
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

        .pricing-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            background: #667eea;
            color: white;
            margin-left: 5px;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
        }

        .image-preview {
            max-width: 200px;
            max-height: 150px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            display: none;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full-width {
                grid-column: span 1;
            }

            .price-row,
            .price-row-4 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <h1><i class="fas fa-cog"></i> Travol Morocco Admin</h1>
        <div class="nav-links">
            <a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
            <a href="packages.php"><i class="fas fa-box"></i> Packages</a>
            <a href="destinations.php"><i class="fas fa-map-marked-alt"></i> Destinations</a>
            <a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
            <a href="analytics.php"><i class="fas fa-chart-bar"></i> Analytics</a>
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
                    <?php echo count(array_unique(array_column($data['excursions'], 'category'))); ?>
                </div>
                <div class="label">Categories</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <div class="number">
                    <?php echo count(array_filter($data['excursions'], fn($e) => strpos($e['duration'], 'Full') !== false)); ?>
                </div>
                <div class="label">Full Day Tours</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-tag"></i>
                <div class="number">
                    <?php echo count(array_unique(array_column($data['excursions'], 'pricingType'))); ?>
                </div>
                <div class="label">Pricing Types</div>
            </div>
        </div>

        <!-- Add/Edit Form -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas <?php echo $editExcursion ? 'fa-edit' : 'fa-plus-circle'; ?>"></i>
                    <?php echo $editExcursion ? 'Edit Excursion' : 'Add New Excursion'; ?>
                </h2>
                <?php if ($editExcursion): ?>
                    <a href="index.php" class="btn" style="background: rgba(255,255,255,0.2); color: white;">
                        <i class="fas fa-times"></i> Cancel Edit
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="excursionForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?php echo $editExcursion ? 'edit' : 'add'; ?>">
                    <?php if ($editExcursion): ?>
                        <input type="hidden" name="id" value="<?php echo $editExcursion['id']; ?>">
                    <?php endif; ?>

                    <div class="form-grid">
                        <div class="form-group">
                            <label><i class="fas fa-heading"></i> Tour Title *</label>
                            <input type="text" name="title" required value="<?php echo htmlspecialchars($editExcursion['title'] ?? ''); ?>" placeholder="e.g., Medina Tour">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-tag"></i> Category *</label>
                            <select name="category" required>
                                <?php foreach ($categories as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" <?php echo ($editExcursion['category'] ?? '') === $key ? 'selected' : ''; ?>>
                                        <?php echo $label; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-clock"></i> Duration *</label>
                            <input type="text" name="duration" required value="<?php echo htmlspecialchars($editExcursion['duration'] ?? 'Full Day'); ?>" placeholder="e.g., Full Day, Half Day, Evening">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt"></i> Location *</label>
                            <input type="text" name="location" required value="<?php echo htmlspecialchars($editExcursion['location'] ?? 'Marrakech'); ?>" placeholder="e.g., Marrakech, Atlas Mountains">
                        </div>

                        <div class="form-group full-width">
                            <label><i class="fas fa-image"></i> Image URL *</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="url" name="image" id="imageUrl" required value="<?php echo htmlspecialchars($editExcursion['image'] ?? ''); ?>" placeholder="https://..." style="flex: 1;">
                                <button type="button" class="btn btn-primary" onclick="document.getElementById('fileUpload').click();" style="white-space: nowrap;">
                                    <i class="fas fa-upload"></i> Upload
                                </button>
                                <input type="file" id="fileUpload" accept="image/*" style="display: none;" onchange="uploadImage(this)">
                            </div>
                            <?php if (!empty($editExcursion['image'])): ?>
                                <img src="<?php echo htmlspecialchars($editExcursion['image']); ?>" class="image-preview" style="display: block; max-width: 200px; margin-top: 10px;" onerror="this.style.display='none'">
                            <?php endif; ?>
                            <div id="imagePreview" style="margin-top: 10px;"></div>
                        </div>

                        <div class="form-group full-width">
                            <label><i class="fas fa-align-left"></i> Description *</label>
                            <textarea name="description" rows="4" required placeholder="Describe the tour..."><?php echo htmlspecialchars($editExcursion['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-tag"></i> Price Tag Display *</label>
                            <input type="text" name="priceTag" required value="<?php echo htmlspecialchars($editExcursion['priceTag'] ?? 'From 350 MAD'); ?>" placeholder="e.g., From 350 MAD, 450 MAD per person">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-calculator"></i> Pricing Type *</label>
                            <select name="pricing_type" id="pricingType" required onchange="updatePricingFields()">
                                <option value="">Select pricing type...</option>
                                <?php foreach ($pricingTypes as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" data-description="<?php echo $label; ?>" <?php echo ($editExcursion['pricingType'] ?? '') === $key ? 'selected' : ''; ?>>
                                        <?php echo $label; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Dynamic Pricing Fields - Simple and Clear -->
                    <div id="pricingFields"></div>

                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="submit" class="btn btn-primary" style="padding: 12px 30px;">
                            <i class="fas fa-save"></i> <?php echo $editExcursion ? 'Update Excursion' : 'Save Excursion'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Excursions List -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-list"></i> All Excursions (<?php echo count($data['excursions']); ?>)</h2>
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
                                <th>Duration</th>
                                <th>Pricing Type</th>
                                <th>Price Tag</th>
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
                                            style="width: 50px; height: 40px; object-fit: cover; border-radius: 4px;"
                                            onerror="this.src='https://via.placeholder.com/50x40?text=No+Image'">
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($excursion['title']); ?></strong>
                                        <span class="pricing-badge"><?php echo $excursion['pricingType']; ?></span>
                                    </td>
                                    <td>
                                        <span class="category-badge category-<?php echo $excursion['category']; ?>">
                                            <?php echo ucfirst($excursion['category']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($excursion['duration']); ?></td>
                                    <td><?php echo $excursion['pricingType']; ?></td>
                                    <td><?php echo htmlspecialchars($excursion['priceTag']); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="?edit=<?php echo $excursion['id']; ?>" class="btn btn-edit btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?delete=<?php echo $excursion['id']; ?>"
                                                class="btn btn-delete btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this excursion?');"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <a href="../pages/excursions.php#tour-<?php echo $excursion['id']; ?>"
                                                target="_blank"
                                                class="btn btn-view btn-sm"
                                                title="View on site">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($data['excursions'])): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 40px;">
                                        <i class="fas fa-info-circle" style="font-size: 48px; color: #ccc;"></i>
                                        <p style="margin-top: 10px;">No excursions found. Click "Add New Excursion" to get started!</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> Travol Morocco Admin Panel. All rights reserved.</p>
    </div>

    <script>
        // Pricing type templates - Simple and clear for non-technical users
        const pricingTemplates = {
            standard: `
                <div class="pricing-card">
                    <h3><i class="fas fa-users"></i> Standard Tour Pricing</h3>
                    <div class="info-text">
                        <i class="fas fa-info-circle"></i> This tour has both Group and Private options with Adult/Child pricing
                    </div>
                    <div style="margin-bottom: 20px;">
                        <h4 style="color: #667eea; margin-bottom: 10px;">🏷️ Group Tour (Shared with others)</h4>
                        <div class="price-row">
                            <div class="form-group">
                                <label>Adult Price (MAD)</label>
                                <input type="number" name="group_adult" value="<?php echo $editExcursion['groupPrice']['adult'] ?? 300; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Child Price (MAD)</label>
                                <input type="number" name="group_child" value="<?php echo $editExcursion['groupPrice']['child'] ?? 150; ?>" required>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 style="color: #667eea; margin-bottom: 10px;">⭐ Private Tour (Just your group)</h4>
                        <div class="price-row">
                            <div class="form-group">
                                <label>Adult Price (MAD)</label>
                                <input type="number" name="private_adult" value="<?php echo $editExcursion['privatePrice']['adult'] ?? 650; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Child Price (MAD)</label>
                                <input type="number" name="private_child" value="<?php echo $editExcursion['privatePrice']['child'] ?? 325; ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
            `,

            quad: `
                <div class="pricing-card">
                    <h3><i class="fas fa-motorcycle"></i> Quad Biking Pricing</h3>
                    <div class="info-text">
                        <i class="fas fa-info-circle"></i> Different prices for Driver and Passenger (no private option)
                    </div>
                    <div class="price-row">
                        <div class="form-group">
                            <label>Driver Price (MAD)</label>
                            <input type="number" name="driver_price" value="<?php echo $editExcursion['groupPrice']['adult'] ?? 450; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Passenger Price (MAD)</label>
                            <input type="number" name="passenger_price" value="<?php echo $editExcursion['groupPrice']['child'] ?? 300; ?>" required>
                        </div>
                    </div>
                </div>
            `,

            balloon: `
                <div class="pricing-card">
                    <h3><i class="fas fa-hot-air-balloon"></i> Hot Air Balloon Pricing</h3>
                    <div class="info-text">
                        <i class="fas fa-info-circle"></i> Four different flight classes (same price for all ages)
                    </div>
                    <div class="price-row-4">
                        <div class="form-group">
                            <label>Classic Flight (MAD)</label>
                            <input type="number" name="classic_price" value="<?php echo $editExcursion['flightClasses']['classic'] ?? 1500; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Premium Flight (MAD)</label>
                            <input type="number" name="premium_price" value="<?php echo $editExcursion['flightClasses']['premium'] ?? 2400; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>VIP Flight (MAD)</label>
                            <input type="number" name="vip_price" value="<?php echo $editExcursion['flightClasses']['vip'] ?? 4500; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Royal Flight (MAD)</label>
                            <input type="number" name="royal_price" value="<?php echo $editExcursion['flightClasses']['royal'] ?? 5200; ?>" required>
                        </div>
                    </div>
                </div>
            `,

            perBuggy: `
                <div class="pricing-card">
                    <h3><i class="fas fa-car"></i> Buggy Adventure Pricing</h3>
                    <div class="info-text">
                        <i class="fas fa-info-circle"></i> Fixed price per buggy (up to 2 people per buggy)
                    </div>
                    <div class="form-group">
                        <label>Price Per Buggy (MAD)</label>
                        <input type="number" name="price_per_buggy" value="<?php echo $editExcursion['pricePerBuggy'] ?? 1600; ?>" required>
                    </div>
                </div>
            `,

            perPerson: `
                <div class="pricing-card">
                    <h3><i class="fas fa-user"></i> Per Person Pricing</h3>
                    <div class="info-text">
                        <i class="fas fa-info-circle"></i> Same price for everyone (no child discount, no private option)
                    </div>
                    <div class="form-group">
                        <label>Price Per Person (MAD)</label>
                        <input type="number" name="price_per_person" value="<?php echo $editExcursion['pricePerPerson'] ?? 350; ?>" required>
                    </div>
                </div>
            `,

            scooter: `
                <div class="pricing-card">
                    <h3><i class="fas fa-motorcycle"></i> Electric Scooter Pricing</h3>
                    <div class="info-text">
                        <i class="fas fa-info-circle"></i> Fixed price: 450 MAD for both Medina and Palm Grove tours
                    </div>
                    <div style="background: #e8f5e8; padding: 15px; border-radius: 5px;">
                        <p><strong>Medina Tour:</strong> 450 MAD</p>
                        <p><strong>Palm Grove Tour:</strong> 450 MAD</p>
                        <p style="font-size: 12px; color: #666; margin-top: 10px;">Price is fixed and cannot be changed</p>
                    </div>
                </div>
            `,

            adultChild: `
                <div class="pricing-card">
                    <h3><i class="fas fa-family"></i> Adult/Child Only Pricing</h3>
                    <div class="info-text">
                        <i class="fas fa-info-circle"></i> Different prices for adults and children (no private/group options)
                    </div>
                    <div class="price-row">
                        <div class="form-group">
                            <label>Adult Price (MAD)</label>
                            <input type="number" name="adult_price" value="<?php echo $editExcursion['prices']['adult'] ?? 500; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Child Price (MAD)</label>
                            <input type="number" name="child_price" value="<?php echo $editExcursion['prices']['child'] ?? 250; ?>" required>
                        </div>
                    </div>
                </div>
            `
        };

        function updatePricingFields() {
            const pricingType = document.getElementById('pricingType').value;
            const container = document.getElementById('pricingFields');

            if (pricingType && pricingTemplates[pricingType]) {
                container.innerHTML = pricingTemplates[pricingType];
            } else {
                container.innerHTML = '';
            }
        }

        // Image upload function
        function uploadImage(input) {
            if (input.files && input.files[0]) {
                const formData = new FormData();
                formData.append('image_file', input.files[0]);

                fetch('upload.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('imageUrl').value = data.url;
                            const preview = document.createElement('img');
                            preview.src = data.url;
                            preview.className = 'image-preview';
                            preview.style.display = 'block';
                            document.getElementById('imagePreview').innerHTML = '';
                            document.getElementById('imagePreview').appendChild(preview);
                        } else {
                            alert('Upload failed: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Upload failed');
                    });
            }
        }

        // Run on page load
        document.addEventListener('DOMContentLoaded', function() {
            updatePricingFields();
        });
    </script>
</body>

</html>