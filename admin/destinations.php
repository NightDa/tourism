<?php
require_once 'config.php';
requireLogin();

$data = loadDestinations();
$message = '';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    foreach ($data['destinations'] as $key => $dest) {
        if ($dest['id'] == $id) {
            unset($data['destinations'][$key]);
            break;
        }
    }
    $data['destinations'] = array_values($data['destinations']);
    saveDestinations($data);
    $message = ['type' => 'success', 'text' => 'Destination deleted successfully!'];
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        // Create slug from name
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['name'])));

        // Handle gallery images
        $gallery = [];
        if (!empty($_POST['gallery'])) {
            $gallery = array_filter(array_map('trim', explode("\n", $_POST['gallery'])));
        }

        // Handle highlights
        $highlights = [];
        if (!empty($_POST['highlights'])) {
            $highlights = array_filter(array_map('trim', explode("\n", $_POST['highlights'])));
        }

        $destination = [
            'id' => $id ?: getNextDestinationId($data),
            'name' => $_POST['name'],
            'slug' => $slug,
            'short_description' => $_POST['short_description'],
            'description' => $_POST['description'],
            'image' => $_POST['image'],
            'gallery' => $gallery,
            'highlights' => $highlights,
            'best_time' => $_POST['best_time'],
            'tour_count' => (int)$_POST['tour_count'],
            'price_from' => (int)$_POST['price_from'],
            'featured' => isset($_POST['featured']) ? true : false
        ];

        if ($_POST['action'] === 'add') {
            $data['destinations'][] = $destination;
            $message = ['type' => 'success', 'text' => 'Destination added successfully!'];
        } elseif ($_POST['action'] === 'edit') {
            foreach ($data['destinations'] as $key => $d) {
                if ($d['id'] == $id) {
                    $data['destinations'][$key] = $destination;
                    break;
                }
            }
            $message = ['type' => 'success', 'text' => 'Destination updated successfully!'];
        }

        saveDestinations($data);
    }
}

// Get destination for editing
$editDestination = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    foreach ($data['destinations'] as $d) {
        if ($d['id'] == $editId) {
            $editDestination = $d;
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
    <title>Manage Destinations - Travol Morocco Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f5f5;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            font-weight: 500;
            color: #555;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #e1e1e1;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
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

        .featured-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            background: #ffd700;
            color: #333;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .action-buttons .btn {
            padding: 5px 10px;
            font-size: 12px;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full-width {
                grid-column: span 1;
            }

            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>
</head>

<body>
    <div class="navbar">
        <h1><i class="fas fa-map-marked-alt"></i> Manage Destinations</h1>
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
                <div class="number"><?php echo count($data['destinations']); ?></div>
                <div class="label">Total Destinations</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-star"></i>
                <div class="number">
                    <?php echo count(array_filter($data['destinations'], function ($d) {
                        return $d['featured'] ?? false;
                    })); ?>
                </div>
                <div class="label">Featured</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-tag"></i>
                <div class="number">
                    <?php echo array_sum(array_column($data['destinations'], 'tour_count')); ?>
                </div>
                <div class="label">Total Tours</div>
            </div>
        </div>

        <!-- Add/Edit Form -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas <?php echo $editDestination ? 'fa-edit' : 'fa-plus-circle'; ?>"></i>
                    <?php echo $editDestination ? 'Edit Destination' : 'Add New Destination'; ?>
                </h2>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="<?php echo $editDestination ? 'edit' : 'add'; ?>">
                    <?php if ($editDestination): ?>
                        <input type="hidden" name="id" value="<?php echo $editDestination['id']; ?>">
                    <?php endif; ?>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Destination Name *</label>
                            <input type="text" name="name" required value="<?php echo $editDestination['name'] ?? ''; ?>">
                        </div>

                        <div class="form-group">
                            <label>Tour Count</label>
                            <input type="number" name="tour_count" value="<?php echo $editDestination['tour_count'] ?? 0; ?>">
                        </div>

                        <div class="form-group">
                            <label>Price From (MAD)</label>
                            <input type="number" name="price_from" value="<?php echo $editDestination['price_from'] ?? 350; ?>">
                        </div>

                        <div class="form-group">
                            <label>Best Time to Visit</label>
                            <input type="text" name="best_time" value="<?php echo $editDestination['best_time'] ?? 'Year-round'; ?>">
                        </div>

                        <div class="form-group full-width">
                            <label>Main Image URL *</label>
                            <input type="url" name="image" required value="<?php echo $editDestination['image'] ?? ''; ?>">
                        </div>

                        <div class="form-group full-width">
                            <label>Short Description *</label>
                            <input type="text" name="short_description" required value="<?php echo $editDestination['short_description'] ?? ''; ?>" maxlength="100">
                            <small>Max 100 characters</small>
                        </div>

                        <div class="form-group full-width">
                            <label>Full Description *</label>
                            <textarea name="description" rows="4" required><?php echo $editDestination['description'] ?? ''; ?></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label>Gallery Images (one per line)</label>
                            <textarea name="gallery" rows="3"><?php echo isset($editDestination['gallery']) ? implode("\n", $editDestination['gallery']) : ''; ?></textarea>
                            <small>Enter image URLs, one per line</small>
                        </div>

                        <div class="form-group full-width">
                            <label>Highlights (one per line)</label>
                            <textarea name="highlights" rows="4"><?php echo isset($editDestination['highlights']) ? implode("\n", $editDestination['highlights']) : ''; ?></textarea>
                        </div>

                        <div class="form-group checkbox-group">
                            <label>
                                <input type="checkbox" name="featured" <?php echo ($editDestination['featured'] ?? false) ? 'checked' : ''; ?>>
                                <i class="fas fa-star"></i> Featured Destination
                            </label>
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $editDestination ? 'Update Destination' : 'Save Destination'; ?>
                        </button>
                        <?php if ($editDestination): ?>
                            <a href="destinations.php" class="btn" style="background: #6c757d; color: white;">
                                <i class="fas fa-times"></i> Cancel Edit
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Destinations List -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-list"></i> All Destinations</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Tours</th>
                                <th>Price</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['destinations'] as $dest): ?>
                                <tr>
                                    <td>#<?php echo $dest['id']; ?></td>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($dest['image']); ?>"
                                            alt="<?php echo htmlspecialchars($dest['name']); ?>"
                                            style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($dest['name']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($dest['short_description']); ?></small>
                                    </td>
                                    <td><?php echo $dest['tour_count']; ?></td>
                                    <td><?php echo $dest['price_from']; ?> MAD</td>
                                    <td>
                                        <?php if ($dest['featured'] ?? false): ?>
                                            <span class="featured-badge"><i class="fas fa-star"></i> Featured</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="?edit=<?php echo $dest['id']; ?>" class="btn btn-edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?delete=<?php echo $dest['id']; ?>"
                                                class="btn btn-delete"
                                                onclick="return confirm('Delete this destination?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <a href="../destinations.php#<?php echo $dest['slug']; ?>" target="_blank" class="btn btn-view">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($data['destinations'])): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 40px;">
                                        <i class="fas fa-map-marked-alt" style="font-size: 48px; color: #ccc;"></i>
                                        <p>No destinations found. Add your first destination!</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>