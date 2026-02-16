<?php
require_once 'config.php';
requireLogin();

$data_file = __DIR__ . '/../data/packages.json';
$data = json_decode(file_get_contents($data_file), true);
$packages = $data['packages'] ?? [];
$message = '';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    foreach ($packages as $key => $p) {
        if ($p['id'] == $id) {
            unset($packages[$key]);
            break;
        }
    }
    $packages = array_values($packages);
    file_put_contents($data_file, json_encode(['packages' => $packages, 'last_updated' => date('Y-m-d')], JSON_PRETTY_PRINT));
    $message = ['type' => 'success', 'text' => 'Package deleted successfully!'];
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        // Build itinerary from form
        $itineraire = [];
        for ($i = 1; $i <= 10; $i++) {
            if (!empty($_POST["day_{$i}_title"])) {
                $itineraire[] = [
                    'day' => $i,
                    'title' => $_POST["day_{$i}_title"],
                    'description' => $_POST["day_{$i}_description"]
                ];
            }
        }

        // Build included/not included lists
        $included = array_filter(array_map('trim', explode("\n", $_POST['included'] ?? '')));
        $not_included = array_filter(array_map('trim', explode("\n", $_POST['not_included'] ?? '')));

        // Build gallery
        $gallery = array_filter(array_map('trim', explode("\n", $_POST['gallery'] ?? '')));

        // Get next ID
        $nextId = 1;
        if (!empty($packages)) {
            $ids = array_column($packages, 'id');
            $nextId = max($ids) + 1;
        }

        $package = [
            'id' => $id ?: $nextId,
            'name' => $_POST['name'],
            'duration' => $_POST['duration'],
            'code' => $_POST['code'] ?? '',
            'overview' => $_POST['overview'],
            'price_from' => $_POST['price_from'] ?? 'Prix sur demande',
            'featured' => isset($_POST['featured']) ? true : false,
            'image' => $_POST['image'] ?? '',
            'gallery' => $gallery,
            'itineraire' => $itineraire,
            'included' => $included,
            'not_included' => $not_included,
            'practical_info' => [
                'pickup' => $_POST['pickup_info'] ?? '',
                'medina_access' => $_POST['medina_info'] ?? '',
                'emergency' => $_POST['emergency'] ?? '',
                'notes' => $_POST['notes'] ?? ''
            ]
        ];

        if ($_POST['action'] === 'add') {
            $packages[] = $package;
            $message = ['type' => 'success', 'text' => 'Package added successfully!'];
        } elseif ($_POST['action'] === 'edit') {
            foreach ($packages as $key => $p) {
                if ($p['id'] == $id) {
                    $packages[$key] = $package;
                    break;
                }
            }
            $message = ['type' => 'success', 'text' => 'Package updated successfully!'];
        }

        file_put_contents($data_file, json_encode(['packages' => $packages, 'last_updated' => date('Y-m-d')], JSON_PRETTY_PRINT));
    }
}

// Get package for editing
$editPackage = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    foreach ($packages as $p) {
        if ($p['id'] == $editId) {
            $editPackage = $p;
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
    <title>Manage Packages - Travol Morocco</title>
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
        }

        .message.success {
            background: #d4edda;
            color: #155724;
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

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
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
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #e1e1e1;
        }

        .featured-badge {
            background: #ffd700;
            color: #333;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .day-input {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
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
        <h1><i class="fas fa-box"></i> Manage Packages</h1>
        <div class="nav-links">
            <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="packages.php" class="active"><i class="fas fa-box"></i> Packages</a>
            <a href="destinations.php"><i class="fas fa-map-marked-alt"></i> Destinations</a>
            <a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
            <a href="analytics.php"><i class="fas fa-chart-bar"></i> Analytics</a>
        </div>
        <div class="user-info">
            <span><i class="fas fa-user"></i> <?php echo $_SESSION['admin_username']; ?></span>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="container">
        <?php if ($message): ?>
            <div class="message <?php echo $message['type']; ?>"><?php echo $message['text']; ?></div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas <?php echo $editPackage ? 'fa-edit' : 'fa-plus-circle'; ?>"></i>
                    <?php echo $editPackage ? 'Edit Package' : 'Add New Package'; ?>
                </h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="<?php echo $editPackage ? 'edit' : 'add'; ?>">
                    <?php if ($editPackage): ?>
                        <input type="hidden" name="id" value="<?php echo $editPackage['id']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Package Name *</label>
                        <input type="text" name="name" required value="<?php echo htmlspecialchars($editPackage['name'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Duration *</label>
                        <input type="text" name="duration" required value="<?php echo htmlspecialchars($editPackage['duration'] ?? '6 jours / 5 nuits'); ?>">
                    </div>

                    <div class="form-group">
                        <label>Code</label>
                        <input type="text" name="code" value="<?php echo htmlspecialchars($editPackage['code'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Overview *</label>
                        <textarea name="overview" rows="3" required><?php echo htmlspecialchars($editPackage['overview'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Price From</label>
                        <input type="text" name="price_from" value="<?php echo htmlspecialchars($editPackage['price_from'] ?? 'Prix sur demande'); ?>">
                    </div>

                    <div class="form-group">
                        <label>Main Image URL</label>
                        <input type="url" name="image" value="<?php echo htmlspecialchars($editPackage['image'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Gallery Images (one per line)</label>
                        <textarea name="gallery" rows="3"><?php echo isset($editPackage['gallery']) ? implode("\n", $editPackage['gallery']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="featured" <?php echo ($editPackage['featured'] ?? false) ? 'checked' : ''; ?>>
                            <i class="fas fa-star"></i> Featured Package (show on homepage)
                        </label>
                    </div>

                    <h3 style="margin: 20px 0 10px;">Itinéraire (Jour par Jour)</h3>

                    <?php for ($i = 1; $i <= 8; $i++):
                        $dayData = null;
                        if ($editPackage && isset($editPackage['itineraire'])) {
                            foreach ($editPackage['itineraire'] as $d) {
                                if ($d['day'] == $i) {
                                    $dayData = $d;
                                    break;
                                }
                            }
                        }
                    ?>
                        <div class="day-input">
                            <h4>Jour <?php echo $i; ?></h4>
                            <div class="form-group">
                                <label>Titre</label>
                                <input type="text" name="day_<?php echo $i; ?>_title" value="<?php echo htmlspecialchars($dayData['title'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="day_<?php echo $i; ?>_description" rows="2"><?php echo htmlspecialchars($dayData['description'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    <?php endfor; ?>

                    <h3 style="margin: 20px 0 10px;">Inclus / Non Inclus</h3>

                    <div class="form-group">
                        <label>Inclus (un par ligne)</label>
                        <textarea name="included" rows="5"><?php echo isset($editPackage['included']) ? implode("\n", $editPackage['included']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Non Inclus (un par ligne)</label>
                        <textarea name="not_included" rows="5"><?php echo isset($editPackage['not_included']) ? implode("\n", $editPackage['not_included']) : ''; ?></textarea>
                    </div>

                    <h3 style="margin: 20px 0 10px;">Informations Pratiques</h3>

                    <div class="form-group">
                        <label>Info Pickup</label>
                        <textarea name="pickup_info" rows="2"><?php echo htmlspecialchars($editPackage['practical_info']['pickup'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Info Médina</label>
                        <textarea name="medina_info" rows="2"><?php echo htmlspecialchars($editPackage['practical_info']['medina_access'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Contact Urgence</label>
                        <input type="text" name="emergency" value="<?php echo htmlspecialchars($editPackage['practical_info']['emergency'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" rows="3"><?php echo htmlspecialchars($editPackage['practical_info']['notes'] ?? ''); ?></textarea>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $editPackage ? 'Update Package' : 'Save Package'; ?>
                        </button>
                        <?php if ($editPackage): ?>
                            <a href="packages.php" class="btn" style="background: #6c757d; color: white;">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Packages List -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-list"></i> All Packages</h2>
            </div>
            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Duration</th>
                            <th>Code</th>
                            <th>Price</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($packages as $p): ?>
                            <tr>
                                <td>#<?php echo $p['id']; ?></td>
                                <td><?php echo htmlspecialchars($p['name']); ?></td>
                                <td><?php echo htmlspecialchars($p['duration']); ?></td>
                                <td><?php echo htmlspecialchars($p['code'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($p['price_from']); ?></td>
                                <td>
                                    <?php if ($p['featured'] ?? false): ?>
                                        <span class="featured-badge"><i class="fas fa-star"></i> Featured</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons" style="display: flex; gap: 5px;">
                                        <a href="?edit=<?php echo $p['id']; ?>" class="btn btn-edit btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete=<?php echo $p['id']; ?>" class="btn btn-delete btn-sm" onclick="return confirm('Delete this package?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <a href="../packages.php?id=<?php echo $p['id']; ?>" target="_blank" class="btn btn-view btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($packages)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-box-open" style="font-size: 48px; color: #ccc;"></i>
                                    <p>No packages found. Add your first package!</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>