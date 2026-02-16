<?php
// Start output buffering
ob_start();

// Load destinations from JSON file
$data_file = __DIR__ . '/data/destinations.json';
require_once 'includes/analytics.php';

// Load destinations
if (file_exists($data_file)) {
    $json_data = file_get_contents($data_file);
    $data = json_decode($json_data, true);
    $destinations = $data['destinations'] ?? [];
} else {
    $destinations = [];
}

// Admin check
session_start();
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Get featured destinations
$featured = array_filter($destinations, function ($d) {
    return $d['featured'] ?? false;
});
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Morocco Destinations - Travol Morocco</title>
    <meta name="description" content="Explore the most beautiful destinations in Morocco. From Marrakech to Sahara, discover where to go.">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        .destinations-hero {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./img/morocco-map.jpg');
            background-size: cover;
            background-position: center;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .destinations-hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .destinations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin: 50px 0;
        }

        .destination-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            position: relative;
        }

        .destination-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.2);
        }

        .destination-image {
            height: 250px;
            overflow: hidden;
            position: relative;
        }

        .destination-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .destination-card:hover .destination-image img {
            transform: scale(1.1);
        }

        .featured-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ffd700;
            color: #333;
            padding: 8px 15px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            z-index: 2;
        }

        .destination-content {
            padding: 25px;
        }

        .destination-content h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }

        .destination-short {
            color: #666;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .destination-highlights {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .highlight-tag {
            background: #f0f3ff;
            color: #667eea;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .destination-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
        }

        .meta-item i {
            color: #667eea;
        }

        .destination-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .price {
            font-size: 20px;
            font-weight: 700;
            color: #28a745;
        }

        .price small {
            font-size: 14px;
            font-weight: 400;
            color: #666;
        }

        .explore-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .explore-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .section-title {
            text-align: center;
            margin: 60px 0 40px;
        }

        .section-title h2 {
            font-size: 36px;
            color: #333;
            margin-bottom: 15px;
        }

        .section-title p {
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .destinations-hero h1 {
                font-size: 32px;
            }

            .destinations-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php if ($is_admin): ?>
        <a href="admin/destinations.php" class="admin-quick-link" style="bottom: 140px;">
            <i class="fas fa-map-marked-alt"></i> Manage Destinations
        </a>
    <?php endif; ?>

    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="destinations-hero">
        <div class="container">
            <h1>Discover Morocco's Finest Destinations</h1>
            <p>From imperial cities to golden deserts, find your perfect Moroccan escape</p>
        </div>
    </section>

    <!-- Featured Destinations -->
    <?php if (!empty($featured)): ?>
        <section class="featured-destinations">
            <div class="container">
                <div class="section-title">
                    <h2>Featured Destinations</h2>
                    <p>Most loved places by our travelers</p>
                </div>

                <div class="destinations-grid">
                    <?php foreach ($featured as $dest): ?>
                        <div class="destination-card">
                            <div class="destination-image">
                                <img src="<?php echo htmlspecialchars($dest['image']); ?>" alt="<?php echo htmlspecialchars($dest['name']); ?>">
                                <div class="featured-badge"><i class="fas fa-star"></i> Featured</div>
                            </div>
                            <div class="destination-content">
                                <h3><?php echo htmlspecialchars($dest['name']); ?></h3>
                                <p class="destination-short"><?php echo htmlspecialchars($dest['short_description']); ?></p>

                                <div class="destination-highlights">
                                    <?php
                                    $highlights = array_slice($dest['highlights'] ?? [], 0, 3);
                                    foreach ($highlights as $highlight):
                                    ?>
                                        <span class="highlight-tag"><?php echo htmlspecialchars($highlight); ?></span>
                                    <?php endforeach; ?>
                                </div>

                                <div class="destination-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span><?php echo htmlspecialchars($dest['best_time']); ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-suitcase"></i>
                                        <span><?php echo $dest['tour_count']; ?> tours</span>
                                    </div>
                                </div>

                                <div class="destination-footer">
                                    <div class="price">
                                        <?php echo $dest['price_from']; ?> MAD <small>per day</small>
                                    </div>
                                    <a href="excursions.php?destination=<?php echo $dest['slug']; ?>" class="explore-btn">
                                        Explore <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- All Destinations -->
    <section class="all-destinations">
        <div class="container">
            <div class="section-title">
                <h2>All Moroccan Destinations</h2>
                <p>Explore every corner of Morocco</p>
            </div>

            <div class="destinations-grid">
                <?php foreach ($destinations as $dest): ?>
                    <div class="destination-card">
                        <div class="destination-image">
                            <img src="<?php echo htmlspecialchars($dest['image']); ?>" alt="<?php echo htmlspecialchars($dest['name']); ?>">
                        </div>
                        <div class="destination-content">
                            <h3><?php echo htmlspecialchars($dest['name']); ?></h3>
                            <p class="destination-short"><?php echo htmlspecialchars($dest['short_description']); ?></p>

                            <div class="destination-highlights">
                                <?php
                                $highlights = array_slice($dest['highlights'] ?? [], 0, 3);
                                foreach ($highlights as $highlight):
                                ?>
                                    <span class="highlight-tag"><?php echo htmlspecialchars($highlight); ?></span>
                                <?php endforeach; ?>
                            </div>

                            <div class="destination-meta">
                                <div class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?php echo htmlspecialchars($dest['best_time']); ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-suitcase"></i>
                                    <span><?php echo $dest['tour_count']; ?> tours</span>
                                </div>
                            </div>

                            <div class="destination-footer">
                                <div class="price">
                                    <?php echo $dest['price_from']; ?> MAD <small>per day</small>
                                </div>
                                <a href="excursions.php?destination=<?php echo $dest['slug']; ?>" class="explore-btn">
                                    Explore <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 80px 0; text-align: center;">
        <div class="container">
            <h2 style="font-size: 36px; margin-bottom: 20px;">Ready to Explore Morocco?</h2>
            <p style="font-size: 18px; margin-bottom: 30px; opacity: 0.9;">Book your dream tour to any of these amazing destinations</p>
            <a href="excursions.php" class="explore-btn" style="background: white; color: #667eea; padding: 15px 40px; font-size: 18px;">
                View All Tours <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop">
        <i class="fas fa-chevron-up"></i>
    </button>

    <script src="js/script.js"></script>
</body>

</html>
<?php ob_end_flush(); ?>