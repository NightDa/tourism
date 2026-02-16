<?php
require_once 'config.php';
requireLogin();

$analytics_file = __DIR__ . '/../data/analytics.json';

if (file_exists($analytics_file)) {
    $analytics = json_decode(file_get_contents($analytics_file), true);
} else {
    $analytics = ['page_views' => [], 'total_visits' => 0];
}

// Handle reset
if (isset($_POST['reset'])) {
    $analytics = [
        'page_views' => [],
        'total_visits' => 0,
        'last_reset' => date('Y-m-d H:i:s')
    ];
    file_put_contents($analytics_file, json_encode($analytics, JSON_PRETTY_PRINT));
    header('Location: analytics.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - Travol Morocco</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }

        /* Navbar Styles */
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
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar h1 i {
            margin-right: 10px;
        }

        /* Navigation Links */
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
            font-size: 14px;
        }

        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .nav-links a.active {
            background: rgba(255, 255, 255, 0.25);
            font-weight: 600;
        }

        .nav-links a i {
            font-size: 16px;
        }

        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info span {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
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
            font-size: 14px;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Container */
        .analytics-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 40px;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .stat-card .number {
            font-size: 42px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-card .label {
            font-size: 14px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Page Stats Card */
        .page-stats {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .page-stats h2 {
            margin-bottom: 20px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 20px;
        }

        .page-stats h2 i {
            color: #667eea;
        }

        /* Table Styles */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e1e1e1;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #e1e1e1;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .page-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-name {
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-name i {
            color: #667eea;
            width: 20px;
        }

        .page-count {
            background: #667eea;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        /* Progress Bar */
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e1e1e1;
            border-radius: 4px;
            margin-top: 8px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 4px;
            transition: width 0.3s;
        }

        /* Reset Button */
        .reset-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            margin-top: 20px;
        }

        .reset-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }

        .reset-btn i {
            font-size: 14px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            color: #ddd;
        }

        .empty-state h3 {
            color: #333;
            margin-bottom: 10px;
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
                gap: 5px;
            }

            .nav-links a {
                padding: 6px 12px;
                font-size: 13px;
            }

            .user-info {
                width: 100%;
                justify-content: space-between;
                margin-top: 10px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-card .number {
                font-size: 36px;
            }

            .page-stats {
                padding: 15px;
            }

            th,
            td {
                padding: 10px;
            }
        }

        /* Small Mobile */
        @media (max-width: 480px) {
            .navbar h1 {
                font-size: 20px;
            }

            .nav-links {
                flex-wrap: wrap;
            }

            .nav-links a {
                flex: 1 1 calc(50% - 5px);
                justify-content: center;
            }

            .user-info span {
                font-size: 13px;
            }

            .logout-btn {
                padding: 6px 12px;
                font-size: 13px;
            }
        }
    </style>
</head>

<body>
    <div class="navbar">
        <h1><i class="fas fa-chart-bar"></i> Analytics Dashboard</h1>
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

    <div class="analytics-container">
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-eye"></i>
                <div class="number"><?php echo $analytics['total_visits'] ?? 0; ?></div>
                <div class="label">Total Visits</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-file-alt"></i>
                <div class="number"><?php echo count($analytics['page_views'] ?? []); ?></div>
                <div class="label">Pages Tracked</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-calendar"></i>
                <div class="number">
                    <?php
                    $last_reset = isset($analytics['last_reset']) ? date('M d', strtotime($analytics['last_reset'])) : 'Never';
                    echo $last_reset;
                    ?>
                </div>
                <div class="label">Last Reset</div>
            </div>
        </div>

        <div class="page-stats">
            <h2><i class="fas fa-chart-line"></i> Page Views</h2>

            <?php if (!empty($analytics['page_views'])):
                $total = array_sum($analytics['page_views']);
                arsort($analytics['page_views']); // Sort by most viewed
            ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Page</th>
                                <th>Views</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($analytics['page_views'] as $page => $count):
                                $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                            ?>
                                <tr>
                                    <td>
                                        <div class="page-name">
                                            <i class="fas fa-file"></i>
                                            <?php echo htmlspecialchars($page); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="page-count"><?php echo $count; ?></span>
                                    </td>
                                    <td style="width: 200px;">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo $percentage; ?>%;"></div>
                                        </div>
                                        <small style="color: #666;"><?php echo $percentage; ?>%</small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-chart-line"></i>
                    <h3>No Data Yet</h3>
                    <p>Visit some pages to see analytics</p>
                </div>
            <?php endif; ?>
        </div>

        <form method="POST" onsubmit="return confirm('Reset all analytics data? This action cannot be undone.');">
            <button type="submit" name="reset" class="reset-btn">
                <i class="fas fa-trash-alt"></i> Reset Analytics Data
            </button>
        </form>
    </div>
</body>

</html>