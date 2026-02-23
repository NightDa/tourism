<?php
ob_start();
require_once '../includes/analytics.php';
session_start();
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

$packages_file = __DIR__ . '/../data/packages.json';
$packages = [];
if (file_exists($packages_file)) {
    $data = json_decode(file_get_contents($packages_file), true);
    $packages = $data['packages'] ?? [];
}

// For AJAX requests, return JSON data
if (isset($_GET['ajax']) && $_GET['ajax'] == 'get_packages') {
    header('Content-Type: application/json');
    echo json_encode($packages);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Forfaits - Travol Morocco</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        .packages-hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/img/desert-hero.jpg');
            background-size: cover;
            background-position: center;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }

        .packages-hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }

        /* Search and Filter Styles */
        .search-section {
            padding: 40px 0;
            background: #f8f9fa;
        }

        .search-wrapper {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e1e1;
            border-radius: 50px;
            font-size: 16px;
            transition: all 0.3s;
            padding-right: 120px;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-clear {
            position: absolute;
            right: 100px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 14px;
            display: none;
        }

        .search-clear.visible {
            display: block;
        }

        .search-clear:hover {
            color: #667eea;
        }

        /* Filter Buttons */
        .packages-filter {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin: 30px 0;
        }

        .filter-btn {
            padding: 10px 20px;
            border: 2px solid #e1e1e1;
            background: white;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .filter-btn:hover {
            border-color: #667eea;
            color: #667eea;
        }

        .filter-btn.active {
            background: #667eea;
            border-color: #667eea;
            color: white;
        }

        /* Results Count */
        .results-count {
            text-align: center;
            margin: 20px 0;
            color: #666;
            font-size: 14px;
        }

        .results-count span {
            font-weight: 600;
            color: #667eea;
        }

        /* Packages Grid */
        .packages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin: 50px 0;
            transition: opacity 0.3s ease;
            min-height: 400px;
        }

        .packages-grid.loading {
            opacity: 0.5;
            pointer-events: none;
            position: relative;
        }

        .packages-grid.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        .package-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .package-card:hover {
            transform: translateY(-10px);
        }

        .package-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .package-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .package-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ffd700;
            color: #333;
            padding: 5px 15px;
            border-radius: 25px;
            font-weight: 600;
        }

        .package-content {
            padding: 20px;
        }

        .package-content h3 {
            font-size: 22px;
            margin-bottom: 10px;
            color: #333;
        }

        .package-duration {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .package-price {
            font-size: 20px;
            font-weight: bold;
            color: #28a745;
            margin: 15px 0;
        }

        .package-btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .package-detail {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
        }

        .package-detail h1 {
            font-size: 32px;
            margin-bottom: 10px;
            color: #333;
        }

        .itineraire-day {
            border-left: 3px solid #667eea;
            padding-left: 20px;
            margin: 20px 0;
        }

        .day-title {
            font-weight: bold;
            color: #667eea;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .lists {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 30px 0;
        }

        .included ul,
        .not-included ul {
            list-style: none;
            padding: 0;
        }

        .included li {
            color: #28a745;
            margin-bottom: 8px;
        }

        .not-included li {
            color: #dc3545;
            margin-bottom: 8px;
        }

        .included li i,
        .not-included li i {
            margin-right: 8px;
        }

        .practical-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
        }

        .back-btn {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
            margin-left: 10px;
        }

        .no-results {
            grid-column: 1/-1;
            text-align: center;
            padding: 60px;
        }

        .no-results i {
            font-size: 64px;
            color: #ccc;
            margin-bottom: 20px;
        }

        .no-results h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .no-results p {
            color: #666;
        }

        @media (max-width: 768px) {
            .packages-hero h1 {
                font-size: 32px;
            }

            .packages-grid {
                grid-template-columns: 1fr;
            }

            .lists {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php if ($is_admin): ?>
        <a href="../admin/packages.php" class="admin-quick-link" style="bottom: 140px; background: #667eea; color: white; padding: 12px 20px; border-radius: 50px; text-decoration: none; position: fixed; right: 20px; z-index: 999;">
            <i class="fas fa-box"></i> Manage Packages
        </a>
    <?php endif; ?>

    <?php include '../includes/header.php'; ?>

    <section class="packages-hero">
        <div class="container">
            <h1>Nos Forfaits</h1>
            <p>Des circuits tout compris pour vivre le Maroc intensément</p>
        </div>
    </section>

    <?php if (!isset($_GET['id'])): ?>
        <!-- Search and Filter Section (only show on grid view) -->
        <section class="search-section">
            <div class="container">
                <div class="search-wrapper">
                    <input type="text" id="searchInput" class="search-input"
                        placeholder="Search packages by name or description...">
                    <button class="search-clear" id="clearSearch">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>

                <div class="packages-filter">
                    <button class="filter-btn active" data-filter="all">All Packages</button>
                    <button class="filter-btn" data-filter="3">3 Nights</button>
                    <button class="filter-btn" data-filter="5">5 Nights</button>
                    <button class="filter-btn" data-filter="7">7+ Nights</button>
                    <button class="filter-btn" data-filter="featured">Featured</button>
                    <button class="filter-btn" data-filter="imperial">Imperial Cities</button>
                    <button class="filter-btn" data-filter="desert">Desert & Atlas</button>
                </div>

                <div class="results-count" id="resultsCount">
                    Showing <span id="showingCount"><?php echo count($packages); ?></span> of
                    <span id="totalCount"><?php echo count($packages); ?></span> packages
                </div>
            </div>
        </section>
    <?php endif; ?>

    <div class="container">
        <?php if (isset($_GET['id'])):
            $found = false;
            foreach ($packages as $p) {
                if ($p['id'] == $_GET['id']) {
                    $package = $p;
                    $found = true;
                    break;
                }
            }
            if ($found):
        ?>
                <!-- Package Detail View -->
                <div class="package-detail">
                    <h1><?php echo htmlspecialchars($package['name']); ?></h1>
                    <p class="package-duration"><?php echo htmlspecialchars($package['duration']); ?> | Code: <?php echo htmlspecialchars($package['code'] ?? '-'); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($package['overview'])); ?></p>

                    <h2 style="margin: 30px 0 20px;">Itinéraire</h2>
                    <?php foreach ($package['itineraire'] as $day): ?>
                        <div class="itineraire-day">
                            <div class="day-title">Jour <?php echo $day['day']; ?>: <?php echo htmlspecialchars($day['title']); ?></div>
                            <p><?php echo nl2br(htmlspecialchars($day['description'])); ?></p>
                        </div>
                    <?php endforeach; ?>

                    <div class="lists">
                        <div class="included">
                            <h3><i class="fas fa-check-circle" style="color: #28a745;"></i> Inclus</h3>
                            <ul>
                                <?php foreach ($package['included'] as $item): ?>
                                    <li><i class="fas fa-check"></i> <?php echo htmlspecialchars($item); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="not-included">
                            <h3><i class="fas fa-times-circle" style="color: #dc3545;"></i> Non inclus</h3>
                            <ul>
                                <?php foreach ($package['not_included'] as $item): ?>
                                    <li><i class="fas fa-times"></i> <?php echo htmlspecialchars($item); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <?php if (!empty($package['practical_info']['pickup']) || !empty($package['practical_info']['notes']) || !empty($package['practical_info']['medina_access']) || !empty($package['practical_info']['emergency'])): ?>
                        <div class="practical-info">
                            <h3><i class="fas fa-info-circle"></i> Informations pratiques</h3>
                            <?php if (!empty($package['practical_info']['pickup'])): ?>
                                <p><strong>Pickup:</strong> <?php echo nl2br(htmlspecialchars($package['practical_info']['pickup'])); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($package['practical_info']['medina_access'])): ?>
                                <p><strong>Médina:</strong> <?php echo nl2br(htmlspecialchars($package['practical_info']['medina_access'])); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($package['practical_info']['emergency'])): ?>
                                <p><strong>Urgence:</strong> <?php echo htmlspecialchars($package['practical_info']['emergency']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($package['practical_info']['notes'])): ?>
                                <p><strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($package['practical_info']['notes'])); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div style="text-align: center; margin-top: 30px;">
                        <a href="contact.php" class="package-btn">Réserver ce forfait</a>
                        <a href="packages.php" class="back-btn">Retour</a>
                    </div>
                </div>
            <?php else: ?>
                <p style="text-align: center; padding: 50px;">Package non trouvé.</p>
            <?php endif; ?>

        <?php else: ?>
            <!-- Package Grid View with AJAX Loading -->
            <div class="packages-grid" id="packagesGrid">
                <!-- Packages will be loaded here via JavaScript -->
            </div>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Only run on grid view (no ID parameter)
            <?php if (!isset($_GET['id'])): ?>

                let allPackages = [];
                let currentFilter = 'all';
                let currentSearch = '';

                // Load packages via AJAX
                function loadPackages() {
                    $('#packagesGrid').addClass('loading');

                    $.ajax({
                        url: 'packages.php?ajax=get_packages',
                        method: 'GET',
                        dataType: 'json',
                        success: function(packages) {
                            allPackages = packages;
                            filterAndDisplayPackages();
                        },
                        error: function() {
                            console.error('Failed to load packages');
                            $('#packagesGrid').removeClass('loading');
                            $('#packagesGrid').html('<div class="no-results"><i class="fas fa-exclamation-circle"></i><h3>Error Loading Packages</h3><p>Please refresh the page or try again later.</p></div>');
                        }
                    });
                }

                // Filter packages based on current filter and search
                function filterAndDisplayPackages() {
                    let filtered = allPackages;

                    // Apply category filter
                    if (currentFilter !== 'all') {
                        if (currentFilter === 'featured') {
                            filtered = filtered.filter(pkg => pkg.featured === true);
                        } else if (currentFilter === '3') {
                            filtered = filtered.filter(pkg => pkg.duration.includes('3 nights') || pkg.duration.includes('3 nuits'));
                        } else if (currentFilter === '5') {
                            filtered = filtered.filter(pkg => pkg.duration.includes('5 nights') || pkg.duration.includes('5 nuits'));
                        } else if (currentFilter === '7') {
                            filtered = filtered.filter(pkg => pkg.duration.includes('7 nights') || pkg.duration.includes('7 nuits') || pkg.duration.includes('7+'));
                        } else if (currentFilter === 'imperial') {
                            filtered = filtered.filter(pkg =>
                                pkg.name.toLowerCase().includes('imperial') ||
                                pkg.name.toLowerCase().includes('royal') ||
                                pkg.name.toLowerCase().includes('cities')
                            );
                        } else if (currentFilter === 'desert') {
                            filtered = filtered.filter(pkg =>
                                pkg.name.toLowerCase().includes('desert') ||
                                pkg.name.toLowerCase().includes('atlas') ||
                                pkg.name.toLowerCase().includes('canyon') ||
                                pkg.name.toLowerCase().includes('chegaga') ||
                                pkg.name.toLowerCase().includes('agafay')
                            );
                        }
                    }

                    // Apply search filter
                    if (currentSearch.trim() !== '') {
                        const searchTerm = currentSearch.toLowerCase().trim();
                        filtered = filtered.filter(pkg =>
                            pkg.name.toLowerCase().includes(searchTerm) ||
                            pkg.overview.toLowerCase().includes(searchTerm) ||
                            (pkg.code && pkg.code.toLowerCase().includes(searchTerm))
                        );
                    }

                    // Update results count
                    $('#showingCount').text(filtered.length);
                    $('#totalCount').text(allPackages.length);

                    // Show/hide clear button
                    if (currentSearch.trim() !== '') {
                        $('#clearSearch').addClass('visible');
                    } else {
                        $('#clearSearch').removeClass('visible');
                    }

                    // Display packages
                    displayPackages(filtered);
                }

                // Display packages in grid
                function displayPackages(packages) {
                    const grid = $('#packagesGrid');
                    grid.empty();

                    if (packages.length === 0) {
                        grid.append(`
                    <div class="no-results">
                        <i class="fas fa-box-open"></i>
                        <h3>No Packages Found</h3>
                        <p>Try adjusting your search or filter to find what you're looking for.</p>
                    </div>
                `);
                        grid.removeClass('loading');
                        return;
                    }

                    packages.forEach(pkg => {
                        const packageCard = `
                    <div class="package-card">
                        <div class="package-image">
                            <img src="${pkg.image || '../assets/img/package-placeholder.jpg'}" alt="${pkg.name}">
                            ${pkg.featured ? '<span class="package-badge"><i class="fas fa-star"></i> Populaire</span>' : ''}
                        </div>
                        <div class="package-content">
                            <h3>${pkg.name}</h3>
                            <div class="package-duration">${pkg.duration}</div>
                            <p>${pkg.overview.substring(0, 100)}...</p>
                            <div class="package-price">${pkg.price_from}</div>
                            <a href="packages.php?id=${pkg.id}" class="package-btn">Voir détails</a>
                        </div>
                    </div>
                `;
                        grid.append(packageCard);
                    });

                    grid.removeClass('loading');
                }

                // Filter button clicks
                $('.filter-btn').click(function() {
                    $('.filter-btn').removeClass('active');
                    $(this).addClass('active');
                    currentFilter = $(this).data('filter');
                    filterAndDisplayPackages();
                });

                // Search input
                $('#searchInput').on('input', function() {
                    currentSearch = $(this).val();
                    filterAndDisplayPackages();
                });

                // Clear search
                $('#clearSearch').click(function() {
                    $('#searchInput').val('');
                    currentSearch = '';
                    filterAndDisplayPackages();
                });

                // Load packages on page load
                loadPackages();

            <?php endif; ?>
        });
    </script>
</body>

</html>
<?php ob_end_flush(); ?>