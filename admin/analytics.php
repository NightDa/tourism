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
<html>

<head>
    <title>Analytics Dashboard</title>
    <style>
        .analytics-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

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
        }

        .stat-card .number {
            font-size: 48px;
            font-weight: bold;
        }

        .page-stats {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .page-row {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .page-row:hover {
            background: #f5f5f5;
        }

        .page-name {
            font-weight: 600;
            color: #333;
        }

        .page-count {
            background: #667eea;
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
        }

        .reset-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="analytics-container">
        <h1>Website Analytics</h1>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="number"><?php echo $analytics['total_visits'] ?? 0; ?></div>
                <div>Total Visits</div>
            </div>
            <div class="stat-card">
                <div class="number"><?php echo count($analytics['page_views'] ?? []); ?></div>
                <div>Pages Tracked</div>
            </div>
            <div class="stat-card">
                <div class="number">
                    <?php
                    $last_reset = isset($analytics['last_reset']) ? date('M d', strtotime($analytics['last_reset'])) : 'Never';
                    echo $last_reset;
                    ?>
                </div>
                <div>Last Reset</div>
            </div>
        </div>

        <div class="page-stats">
            <h2>Page Views</h2>
            <?php
            if (!empty($analytics['page_views'])):
                arsort($analytics['page_views']); // Sort by most viewed
                foreach ($analytics['page_views'] as $page => $count):
            ?>
                    <div class="page-row">
                        <span class="page-name"><?php echo htmlspecialchars($page); ?></span>
                        <span class="page-count"><?php echo $count; ?> views</span>
                    </div>
                <?php
                endforeach;
            else:
                ?>
                <p>No data yet. Visit some pages to see stats.</p>
            <?php endif; ?>
        </div>

        <form method="POST" onsubmit="return confirm('Reset all analytics data?');">
            <button type="submit" name="reset" class="reset-btn">
                <i class="fas fa-trash"></i> Reset Analytics
            </button>
        </form>
    </div>
</body>

</html>