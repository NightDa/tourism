<?php
// Start output buffering for potential caching later
ob_start();

// Load excursions from JSON file
$data_file = __DIR__ . '/../data/excursions.json';
require_once '../includes/analytics.php';

// Load excursions
if (file_exists($data_file)) {
    $json_data = file_get_contents($data_file);
    $data = json_decode($json_data, true);
    $excursions = $data['excursions'] ?? [];
} else {
    $excursions = [];
}

// For AJAX requests, return JSON data
if (isset($_GET['ajax']) && $_GET['ajax'] == 'get_tours') {
    header('Content-Type: application/json');
    echo json_encode($excursions);
    exit;
}

// Admin check
session_start();
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Travol Morocco - Excursions & Tours</title>
    <!-- SEO Meta Tags -->
    <meta name="description" content="Discover unforgettable Moroccan excursions and day tours from Marrakech.">
    <meta name="keywords" content="Morocco tours, Marrakech excursions, desert tours, atlas mountains">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Travol Morocco">

    <!-- Open Graph for Social Media -->
    <meta property="og:title" content="Travol Morocco - Excursions & Tours">
    <meta property="og:description" content="Professional Moroccan tour agency">
    <meta property="og:image" content="../assets/img/logo.png">
    <meta property="og:type" content="website">

    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/responsive.css" />
    <link rel="stylesheet" href="../assets/css/excursions.css" />

    <!-- jQuery for easy AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* Smooth transitions */
        .excursions-grid {
            transition: opacity 0.3s ease;
            min-height: 400px;
        }

        .excursions-grid.loading {
            opacity: 0.5;
            pointer-events: none;
            position: relative;
        }

        .excursions-grid.loading::after {
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

        /* Search and filter styles */
        .search-wrapper {
            max-width: 600px;
            margin: 20px auto;
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

        /* Filter buttons */
        .excursions-filter {
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

        /* Results count */
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

        /* No results */
        .no-results {
            text-align: center;
            grid-column: 1/-1;
            padding: 60px 20px;
        }

        .no-results i {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 20px;
        }

        .no-results h3 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }

        .no-results p {
            color: #666;
        }

        /* Admin quick link */
        .admin-quick-link {
            position: fixed;
            bottom: 80px;
            right: 20px;
            background: #667eea;
            color: white;
            padding: 12px 20px;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            z-index: 999;
            font-weight: 600;
            transition: all 0.3s;
        }

        .admin-quick-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.6);
            color: white;
        }

        /* Price display under inputs */
        .price-hint {
            display: block;
            margin-top: 5px;
            font-size: 13px;
            color: #667eea;
            font-weight: 500;
        }

        .price-hint i {
            margin-right: 3px;
            font-size: 12px;
        }

        .current-prices {
            background: #f0f3ff;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
            border-left: 3px solid #667eea;
        }

        .current-prices p {
            margin: 5px 0;
        }

        .current-prices .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .current-prices .price-label {
            font-weight: 600;
            color: #333;
        }

        .current-prices .price-value {
            color: #667eea;
            font-weight: 700;
        }

        /* Hotel Suggestions Box */
        .suggestions-box {
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            max-height: 300px;
            overflow-y: auto;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 5px;
        }

        .suggestion-item {
            padding: 12px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.3s;
        }

        .suggestion-item:hover {
            background: #f8f9fa;
        }

        .suggestion-item i {
            margin-right: 10px;
            width: 20px;
            color: #667eea;
        }

        /* Price display directly under inputs */
        .price-under-input {
            margin-top: 8px;
            padding: 8px 12px;
            background: #f0f3ff;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            color: #667eea;
            text-align: center;
            border-left: 4px solid #667eea;
        }
    </style>
</head>

<body>
    <?php if ($is_admin): ?>
        <a href="../admin/index.php" class="admin-quick-link">
            <i class="fas fa-cog"></i> Admin Dashboard
        </a>
    <?php endif; ?>

    <!-- ========== Preloader Start ========== -->
    <div id="preloader">
        <div class="sand-layer"></div>
        <div class="loader-content">
            <img src="../assets/img/Sans titre.png" alt="Travelo Logo" class="preload-logo" />
            <h2 class="loading-text">Discover Morocco...</h2>
        </div>
    </div>
    <!-- ========== Preloader End ========== -->

    <!-- ===========header ==================start-->
    <header>
        <div class="container">
            <nav>
                <div class="logo">
                    <img src="../assets/img/Sans titre.png" alt="Travelo Logo" />
                </div>
                <ul>
                    <div class="btn">
                        <i class="fas fa-times close-btn"></i>
                    </div>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="excursions.php" class="active">Tours</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
                <div class="btn">
                    <i class="fas fa-bars menu-btn"></i>
                </div>
            </nav>
        </div>
    </header>
    <!-- ===========header ==================Close-->

    <!-- =========== Page Hero ==================Start-->
    <section class="excursions-hero">
        <div class="hero-overlay">
            <div class="container">
                <h1>Morocco Excursions & Tours</h1>
                <p>Discover unforgettable experiences from Marrakech and beyond</p>
            </div>
        </div>
    </section>
    <!-- =========== Page Hero ==================Close-->

    <!-- =========== Search and Filter ==================Start-->
    <section class="search-section">
        <div class="container">
            <div class="search-wrapper">
                <input type="text" id="searchInput" class="search-input"
                    placeholder="Search tours by name, location, or description...">
                <button class="search-clear" id="clearSearch">
                    <i class="fas fa-times"></i> Clear
                </button>
            </div>

            <div class="excursions-filter">
                <button class="filter-btn active" data-filter="all">All Tours</button>
                <button class="filter-btn" data-filter="desert">Desert Adventures</button>
                <button class="filter-btn" data-filter="mountain">Mountain Tours</button>
                <button class="filter-btn" data-filter="cultural">Cultural Experiences</button>
                <button class="filter-btn" data-filter="coastal">Coastal Tours</button>
            </div>

            <div class="results-count" id="resultsCount">
                Showing <span id="showingCount"><?php echo count($excursions); ?></span> of
                <span id="totalCount"><?php echo count($excursions); ?></span> tours
            </div>
        </div>
    </section>
    <!-- =========== Search and Filter ==================Close-->

    <!-- =========== Excursions Grid ==================Start-->
    <section class="excursions-section">
        <div class="container">
            <div class="section-header">
                <p class="heading-normal-txt">UNFORGETTABLE EXPERIENCES</p>
                <h2 class="headings">DAY <span>TOURS & EXCURSIONS</span></h2>
            </div>

            <div class="excursions-grid" id="excursionsGrid">
                <!-- Tours will be loaded here via JavaScript -->
            </div>
        </div>
    </section>
    <!-- =========== Excursions Grid ==================Close-->

    <!-- =========== Booking Info ==================Start-->
    <section class="booking-info">
        <div class="container">
            <div class="info-grid">
                <div class="info-card">
                    <i class="fas fa-headset"></i>
                    <h3>24/7 Support</h3>
                    <p>Contact us anytime for assistance</p>
                </div>
                <div class="info-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Best Price Guarantee</h3>
                    <p>Find a lower price? We'll match it!</p>
                </div>
                <div class="info-card">
                    <i class="fas fa-calendar-check"></i>
                    <h3>Easy Booking</h3>
                    <p>Instant confirmation for all tours</p>
                </div>
                <div class="info-card">
                    <i class="fas fa-phone-alt"></i>
                    <h3>Contact Us</h3>
                    <p>+212 655 23 71 96 (WhatsApp)</p>
                </div>
            </div>
        </div>
    </section>
    <!-- =========== Booking Info ==================Close-->

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Booking Modal -->
    <div id="bookingModal" class="booking-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-shopping-cart"></i> Your Booking Cart</h2>
                <span class="close-modal">&times;</span>
            </div>

            <div class="modal-body">
                <!-- Cart Items -->
                <div id="cartItems" class="cart-items">
                    <!-- Cart items will appear here -->
                </div>

                <!-- Add New Item Form -->
                <div class="add-item-section">
                    <h3><i class="fas fa-plus-circle"></i> Add Another Tour</h3>

                    <div class="tour-selector">
                        <select id="modalTourSelect" class="form-control">
                            <option value="">Select a tour...</option>
                            <?php foreach ($excursions as $tour): ?>
                                <option value="<?php echo $tour['id']; ?>"
                                    data-pricing-type="<?php echo $tour['pricingType']; ?>"
                                    data-title="<?php echo htmlspecialchars($tour['title']); ?>"
                                    data-price-tag="<?php echo htmlspecialchars($tour['priceTag']); ?>"

                                    <?php if ($tour['pricingType'] == 'standard'): ?>
                                    data-price-group="<?php echo $tour['groupPrice']['adult']; ?>"
                                    data-price-private="<?php echo $tour['privatePrice']['adult']; ?>"
                                    data-child-group="<?php echo $tour['groupPrice']['child']; ?>"
                                    data-child-private="<?php echo $tour['privatePrice']['child']; ?>"

                                    <?php elseif ($tour['pricingType'] == 'quad'): ?>
                                    data-price-group="<?php echo $tour['groupPrice']['adult']; ?>"
                                    data-child-group="<?php echo $tour['groupPrice']['child']; ?>"

                                    <?php elseif ($tour['pricingType'] == 'balloon'): ?>
                                    data-classic="<?php echo $tour['flightClasses']['classic']; ?>"
                                    data-premium="<?php echo $tour['flightClasses']['premium']; ?>"
                                    data-vip="<?php echo $tour['flightClasses']['vip']; ?>"
                                    data-royal="<?php echo $tour['flightClasses']['royal']; ?>"

                                    <?php elseif ($tour['pricingType'] == 'perBuggy'): ?>
                                    data-price="<?php echo $tour['pricePerBuggy']; ?>"

                                    <?php elseif ($tour['pricingType'] == 'perPerson'): ?>
                                    data-price="<?php echo $tour['pricePerPerson']; ?>"

                                    <?php elseif ($tour['pricingType'] == 'scooter'): ?>
                                    data-price-medina="450"
                                    data-price-palm="450"

                                    <?php elseif ($tour['pricingType'] == 'adultChild'): ?>
                                    data-adult-price="<?php echo $tour['prices']['adult']; ?>"
                                    data-child-price="<?php echo $tour['prices']['child']; ?>"
                                    <?php endif; ?>>
                                    <?php echo $tour['title']; ?> - <?php echo $tour['priceTag']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="booking-form" id="bookingForm" style="display: none;">
                        <!-- Balloon Pricing -->
                        <div id="balloonPricing" style="display: none;">
                            <div class="form-group">
                                <label><i class="fas fa-hot-air-balloon"></i> Select Flight Class</label>
                                <div id="balloonOptions"></div>
                            </div>
                        </div>

                        <!-- Scooter Pricing -->
                        <div id="scooterPricing" style="display: none;">
                            <div class="form-group">
                                <label><i class="fas fa-motorcycle"></i> Select Tour Option</label>
                                <div id="scooterOptions"></div>
                            </div>
                        </div>

                        <!-- Simple Pricing (for perPerson, perBuggy, quad, adultChild) -->
                        <div id="simplePricing" style="display: none;">
                            <div id="priceDisplay"></div>
                        </div>

                        <!-- Date Selection -->
                        <div class="form-group">
                            <label><i class="fas fa-calendar"></i> Select Date</label>
                            <input type="date" id="tourDate" class="form-control" min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <!-- Time selection (only for private tours) -->
                        <div class="form-group" id="timeSelectionGroup" style="display: none;">
                            <label><i class="fas fa-clock"></i> Preferred Time (Private Tours Only)</label>
                            <select id="tourTime" class="form-control">
                                <option value="">Select a time...</option>
                                <option value="08:00">08:00 AM</option>
                                <option value="09:00">09:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="14:00">02:00 PM</option>
                                <option value="15:00">03:00 PM</option>
                                <option value="16:00">04:00 PM</option>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group" id="adultGroup">
                                <label><i class="fas fa-user"></i> <span id="adultLabel">Adults</span></label>
                                <input type="number" id="adults" class="form-control" min="1" value="1">
                                <div class="price-under-input" id="displayAdultPrice">0 MAD</div>
                            </div>

                            <div class="form-group" id="childGroup">
                                <label><i class="fas fa-child"></i> <span id="childLabel">Children (under 12)</span></label>
                                <input type="number" id="children" class="form-control" min="0" value="0">
                                <div class="price-under-input" id="displayChildPrice">0 MAD</div>
                            </div>
                        </div>

                        <!-- Tour Type Selection (only for standard tours) -->
                        <div class="form-group" id="tourTypeSection" style="display: none;">
                            <label><i class="fas fa-users"></i> Tour Type</label>
                            <div class="tour-type-options">
                                <label class="radio-label">
                                    <input type="radio" name="tourType" value="group" checked>
                                    <span>Group Tour</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="tourType" value="private">
                                    <span>Private Tour</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-comment"></i> Special Requests</label>
                            <textarea id="specialRequests" class="form-control" rows="2" placeholder="Any special requirements?"></textarea>
                        </div>

                        <button type="button" id="addToCartBtn" class="primary-btn">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="customer-info-section">
                    <h3><i class="fas fa-user-circle"></i> Your Information</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Full Name *</label>
                            <input type="text" id="customerName" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email *</label>
                            <input type="email" id="customerEmail" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> WhatsApp/Phone *</label>
                            <input type="tel" id="customerPhone" class="form-control" placeholder="+212 XXX XXXXXX" required>
                        </div>
                    </div>

                    <!-- Hotel Search - IMPROVED VERSION -->
                    <div class="form-group" style="position: relative;">
                        <label><i class="fas fa-hotel"></i> Your Hotel/Riad in Marrakech *</label>
                        <input type="text" id="hotelSearch" class="form-control"
                            placeholder="Enter your hotel or riad name..."
                            autocomplete="off"
                            value="Your Marrakech Riad/Hotel">
                        <small style="display: block; margin-top: 5px; color: #666; font-size: 12px;">
                            <i class="fas fa-info-circle"></i> Start typing to see suggestions, or enter your hotel name manually
                        </small>
                        <div id="hotelSuggestions" class="suggestions-box" style="display: none;"></div>
                    </div>

                    <!-- Selected Hotel Info (appears after selection from suggestions) -->
                    <div id="selectedHotelInfo" style="display: none; background: #f0f3ff; padding: 15px; border-radius: 8px; margin-top: 10px;">
                        <h4 style="margin: 0 0 10px 0; color: #333;"><i class="fas fa-check-circle" style="color: #28a745;"></i> Selected Pickup</h4>
                        <p><strong id="selectedHotelName"></strong></p>
                        <p><i class="fas fa-map-marker-alt"></i> Meeting Point: <strong id="selectedPickupPoint"></strong></p>
                        <p><i class="fas fa-clock"></i> Pickup Time: <strong id="selectedPickupTime"></strong></p>
                        <p><i class="fas fa-tag"></i> <strong id="selectedZone"></strong></p>
                        <input type="hidden" id="pickupLocation" name="pickupLocation">
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="payment-summary" id="paymentSummary">
                    <h3><i class="fas fa-credit-card"></i> Payment Summary</h3>
                    <div class="summary-details">
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span id="subtotal">0 MAD</span>
                        </div>
                        <div class="summary-row">
                            <span>Payment Method:</span>
                            <span><i class="fas fa-money-bill-wave"></i> Cash on Arrival</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total to Pay:</span>
                            <span id="totalAmount">0 MAD</span>
                        </div>
                    </div>

                    <div class="payment-note">
                        <i class="fas fa-info-circle"></i> Pay in cash (MAD, EUR, USD) when we meet
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="secondary-btn" id="continueShoppingBtn">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </button>
                <button type="button" class="primary-btn" id="submitBookingBtn">
                    <i class="fas fa-check"></i> Confirm Booking
                </button>
            </div>
        </div>
    </div>

    <!-- Cart Icon -->
    <div id="cartIcon" class="cart-icon" style="display: none;">
        <i class="fas fa-shopping-cart"></i>
        <span id="cartCount" class="cart-count">0</span>
    </div>

    <style>
        /* Modal Styles */
        .booking-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow-y: auto;
        }

        .modal-content {
            background: white;
            margin: 30px auto;
            padding: 0;
            width: 90%;
            max-width: 900px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 i {
            margin-right: 10px;
        }

        .close-modal {
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s;
        }

        .close-modal:hover {
            color: #ff4444;
        }

        .modal-body {
            padding: 30px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 20px 30px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        /* Cart Items */
        .cart-items {
            margin-bottom: 30px;
        }

        .cart-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
            position: relative;
        }

        .cart-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .cart-item-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .cart-item-price {
            font-size: 18px;
            font-weight: 700;
            color: #667eea;
        }

        .cart-item-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-bottom: 10px;
            color: #666;
        }

        .cart-item-details i {
            margin-right: 5px;
            color: #667eea;
        }

        .remove-item {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ff4444;
            color: white;
            border: none;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s;
        }

        .remove-item:hover {
            background: #cc0000;
            transform: scale(1.1);
        }

        /* Add Item Section */
        .add-item-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .add-item-section h3 {
            margin-bottom: 20px;
            color: #333;
        }

        .tour-selector {
            margin-bottom: 20px;
        }

        .booking-form {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px dashed #ddd;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .tour-type-options {
            display: flex;
            gap: 20px;
        }

        .radio-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .radio-label input[type="radio"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        /* Customer Info Section */
        .customer-info-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .customer-info-section h3 {
            margin-bottom: 20px;
            color: #333;
        }

        /* Payment Summary */
        .payment-summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
        }

        .payment-summary h3 {
            margin-bottom: 15px;
            color: white;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .summary-row.total {
            font-size: 20px;
            font-weight: bold;
            border-bottom: none;
            margin-top: 10px;
        }

        .payment-note {
            margin-top: 15px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            font-size: 14px;
        }

        .payment-note i {
            margin-right: 5px;
        }

        /* Cart Icon */
        .cart-icon {
            position: fixed;
            bottom: 130px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            z-index: 999;
            transition: all 0.3s;
        }

        .cart-icon:hover {
            transform: scale(1.1);
        }

        .cart-icon i {
            font-size: 24px;
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4444;
            color: white;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .primary-btn,
        .secondary-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .primary-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .primary-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .secondary-btn {
            background: #e1e1e1;
            color: #333;
        }

        .secondary-btn:hover {
            background: #d1d1d1;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .modal-content {
                width: 95%;
                margin: 15px auto;
            }

            .cart-icon {
                bottom: 100px;
                right: 15px;
                width: 50px;
                height: 50px;
            }
        }
    </style>

    <script src="../assets/js/script.js"></script>

    <script>
        $(document).ready(function() {
            // ========== TOUR DISPLAY CODE ==========
            let allTours = [];
            let currentFilter = 'all';
            let currentSearch = '';
            let selectedTourId = null;

            // Load tours via AJAX
            function loadTours() {
                $('#excursionsGrid').addClass('loading');

                $.ajax({
                    url: 'excursions.php?ajax=get_tours',
                    method: 'GET',
                    dataType: 'json',
                    success: function(tours) {
                        allTours = tours;
                        filterAndDisplayTours();
                    },
                    error: function() {
                        console.error('Failed to load tours');
                        $('#excursionsGrid').removeClass('loading');
                        $('#excursionsGrid').html('<div class="no-results"><i class="fas fa-exclamation-circle"></i><h3>Error Loading Tours</h3><p>Please refresh the page or try again later.</p></div>');
                    }
                });
            }

            // Filter and display tours
            function filterAndDisplayTours() {
                let filtered = allTours;

                if (currentFilter !== 'all') {
                    filtered = filtered.filter(tour => tour.category === currentFilter);
                }

                if (currentSearch.trim() !== '') {
                    const searchTerm = currentSearch.toLowerCase().trim();
                    filtered = filtered.filter(tour =>
                        tour.title.toLowerCase().includes(searchTerm) ||
                        tour.description.toLowerCase().includes(searchTerm) ||
                        tour.location.toLowerCase().includes(searchTerm)
                    );
                }

                $('#showingCount').text(filtered.length);
                $('#totalCount').text(allTours.length);

                if (currentSearch.trim() !== '') {
                    $('#clearSearch').addClass('visible');
                } else {
                    $('#clearSearch').removeClass('visible');
                }

                displayTours(filtered);
            }

            // Display tours in grid
            function displayTours(tours) {
                const grid = $('#excursionsGrid');
                grid.empty();

                if (tours.length === 0) {
                    grid.append(`
                        <div class="no-results">
                            <i class="fas fa-map-marked-alt"></i>
                            <h3>No Tours Found</h3>
                            <p>Try adjusting your search or filter to find what you're looking for.</p>
                        </div>
                    `);
                    grid.removeClass('loading');
                    return;
                }

                tours.forEach(tour => {
                    let pricingHtml = '';

                    // Dynamic pricing based on pricingType
                    switch (tour.pricingType) {
                        case 'standard':
                            pricingHtml = `
                                <div class="price-group">
                                    <h4>Group Tour</h4>
                                    <p>Adult: ${tour.groupPrice.adult} MAD</p>
                                    <p>Child: ${tour.groupPrice.child} MAD</p>
                                </div>
                                <div class="price-group">
                                    <h4>Private Tour</h4>
                                    <p>Adult: ${tour.privatePrice.adult} MAD</p>
                                    <p>Child: ${tour.privatePrice.child} MAD</p>
                                </div>
                            `;
                            break;

                        case 'quad':
                            pricingHtml = `
                                <div class="pricing single-price">
                                    <h4>Quad Biking</h4>
                                    <p><strong>Driver:</strong> ${tour.groupPrice.adult} MAD</p>
                                    <p><strong>Passenger:</strong> ${tour.groupPrice.child} MAD</p>
                                    <p><small class="text-muted">No private option available</small></p>
                                </div>
                            `;
                            break;

                        case 'balloon':
                            pricingHtml = `
                                <div class="price-options">
                                    <h4>Hot Air Balloon Flights</h4>
                                    <p><strong>Classic Flight:</strong> ${tour.flightClasses.classic} MAD</p>
                                    <p><strong>Premium Flight:</strong> ${tour.flightClasses.premium} MAD</p>
                                    <p><strong>VIP Flight:</strong> ${tour.flightClasses.vip} MAD</p>
                                    <p><strong>Royal Flight:</strong> ${tour.flightClasses.royal} MAD</p>
                                    <p><small class="text-muted">Price per person - all ages</small></p>
                                </div>
                            `;
                            break;

                        case 'perBuggy':
                            pricingHtml = `
                                <div class="pricing single-price">
                                    <h4>Buggy Adventure</h4>
                                    <p><strong>${tour.pricePerBuggy} MAD per buggy</strong></p>
                                    <p><small class="text-muted">Up to 2 people per buggy</small></p>
                                </div>
                            `;
                            break;

                        case 'perPerson':
                            pricingHtml = `
                                <div class="pricing single-price">
                                    <h4>Price</h4>
                                    <p><strong>${tour.pricePerPerson} MAD per person</strong></p>
                                    <p><small class="text-muted">All ages same price</small></p>
                                </div>
                            `;
                            break;

                        case 'scooter':
                            pricingHtml = `
                                <div class="price-options">
                                    <h4>Scooter Options</h4>
                                    ${tour.options.map(opt => 
                                        `<p><strong>${opt.name}:</strong> ${opt.price} MAD</p>`
                                    ).join('')}
                                    <p><small class="text-muted">Price per scooter</small></p>
                                </div>
                            `;
                            break;

                        case 'adultChild':
                            pricingHtml = `
                                <div class="pricing single-price">
                                    <h4>Price</h4>
                                    <p><strong>Adult:</strong> ${tour.prices.adult} MAD</p>
                                    <p><strong>Child:</strong> ${tour.prices.child} MAD</p>
                                </div>
                            `;
                            break;

                        default:
                            pricingHtml = `<p>Price: ${tour.priceTag}</p>`;
                    }

                    const tourCard = `
                        <div class="excursion-card" data-category="${tour.category}" data-tour-id="${tour.id}" data-pricing-type="${tour.pricingType}">
                            <div class="excursion-image">
                                <img src="${tour.image}" alt="${tour.title}" onerror="this.src='../assets/img/placeholder.jpg'">
                                <div class="price-tag">${tour.priceTag}</div>
                            </div>
                            <div class="excursion-content">
                                <h3>${tour.title}</h3>
                                <div class="excursion-meta">
                                    <span><i class="fas fa-clock"></i> ${tour.duration}</span>
                                    <span><i class="fas fa-map-marker-alt"></i> ${tour.location}</span>
                                </div>
                                <p>${tour.description.substring(0, 150)}...</p>
                                <div class="pricing">
                                    ${pricingHtml}
                                </div>
                                <a href="#" class="book-btn" data-tour-id="${tour.id}" data-pricing-type="${tour.pricingType}">Book Now</a>
                            </div>
                        </div>
                    `;

                    grid.append(tourCard);
                });

                grid.removeClass('loading');
            }

            // ========== BOOKING CART CODE ==========
            let cart = [];
            // ========== HOTEL PICKUP SYSTEM ==========
            let hotelsData = null;
            let hotelsList = [];

            // Load hotels from JSON
            $.ajax({
                url: '../data/hotels.json',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    hotelsData = data;
                    // Flatten hotels list for search
                    Object.keys(data.zones).forEach(zoneKey => {
                        const zone = data.zones[zoneKey];
                        zone.hotels.forEach(hotel => {
                            hotelsList.push({
                                ...hotel,
                                zone: zone.name,
                                zoneKey: zoneKey
                            });
                        });
                    });
                    console.log('Hotels loaded:', hotelsList.length);
                },
                error: function() {
                    console.log('Failed to load hotels data');
                }
            });

            // Initialize hotel field
            $('#hotelSearch').val('Your Marrakech Riad/Hotel');
            $('#pickupLocation').val('Your Marrakech Riad/Hotel');

            // Hotel search - IMPROVED VERSION
            let hotelTimer;
            $('#hotelSearch').on('input', function() {
                const search = $(this).val().toLowerCase();

                // Always update the pickup location with what user typed
                $('#pickupLocation').val($(this).val());

                clearTimeout(hotelTimer);

                if (search.length < 2) {
                    $('#hotelSuggestions').hide();
                    return;
                }

                hotelTimer = setTimeout(() => {
                    const matches = hotelsList.filter(hotel =>
                        hotel.name.toLowerCase().includes(search)
                    ).slice(0, 8); // Show max 8 results

                    displayHotelSuggestions(matches);
                }, 300);
            });

            function displayHotelSuggestions(hotels) {
                const box = $('#hotelSuggestions');
                box.empty();

                if (hotels.length === 0) {
                    box.html('<div class="suggestion-item" style="color: #999;">No matching hotels found - you can continue typing</div>');
                    box.show();
                    return;
                }

                hotels.forEach(hotel => {
                    const hotelJson = JSON.stringify(hotel).replace(/'/g, "&#39;");

                    const item = $(`
                        <div class="suggestion-item" data-hotel='${hotelJson}'>
                            <i class="fas fa-hotel" style="color: #667eea;"></i>
                            <span style="font-weight: 500;">${hotel.name}</span>
                            <br>
                            <span style="font-size: 12px; color: #666; margin-left: 25px;">
                                <i class="fas fa-map-marker-alt"></i> Pickup: ${hotel.pickup_point}
                                <i class="fas fa-clock"></i> ${hotel.pickup_times ? Object.values(hotel.pickup_times)[0] : 'Time TBD'}
                            </span>
                        </div>
                    `);

                    item.on('click', function() {
                        try {
                            const hotelData = $(this).data('hotel');
                            selectHotel(hotelData);
                        } catch (e) {
                            console.log('Error selecting hotel:', e);
                        }
                    });

                    box.append(item);
                });

                box.show();
            }

            function selectHotel(hotel) {
                const tourTitle = $('#modalTourSelect option:selected').text().toLowerCase();

                // Map tour to activity key
                let activityKey = '';
                if (tourTitle.includes('agafay') || tourTitle.includes('quad') || tourTitle.includes('desert') || tourTitle.includes('buggy') || tourTitle.includes('dromedary')) {
                    activityKey = 'agafay';
                } else if (tourTitle.includes('souk') || tourTitle.includes('medina')) {
                    activityKey = 'souk_medina';
                } else if (tourTitle.includes('diner nejjarine')) {
                    activityKey = 'diner_nejjarine';
                } else if (tourTitle.includes('cooking') || tourTitle.includes('cuisine')) {
                    activityKey = 'cooking_class';
                } else if (tourTitle.includes('essaouira')) {
                    activityKey = 'essaouira';
                } else if (tourTitle.includes('diner nouba') || tourTitle.includes('comptoir') || tourTitle.includes('dar zellij')) {
                    activityKey = 'diner_nouba';
                } else if (tourTitle.includes('ourika') || tourTitle.includes('valley')) {
                    activityKey = 'ourika';
                }

                // Get pickup time for this hotel and activity
                const pickupTime = hotel.pickup_times ? hotel.pickup_times[activityKey] : null;

                if (pickupTime) {
                    // Show selected hotel info with pickup time
                    $('#selectedHotelName').text(hotel.name);
                    $('#selectedPickupPoint').text(hotel.pickup_point);
                    $('#selectedPickupTime').text(pickupTime);
                    $('#selectedZone').text(hotel.zone);

                    // Set the actual pickup location value
                    const pickupValue = `${hotel.name} - ${hotel.pickup_point} at ${pickupTime}`;
                    $('#pickupLocation').val(pickupValue);
                    $('#hotelSearch').val(hotel.name);

                    $('#selectedHotelInfo').slideDown();
                    $('#hotelSuggestions').hide();

                    showNotification('Hotel selected! Pickup: ' + hotel.pickup_point + ' at ' + pickupTime, 'success');
                } else {
                    // No pickup time for this activity - still allow selection
                    $('#selectedHotelName').text(hotel.name);
                    $('#selectedPickupPoint').text(hotel.pickup_point);
                    $('#selectedPickupTime').text('Please confirm with us');
                    $('#selectedZone').text(hotel.zone || 'Medina');

                    const pickupValue = `${hotel.name} - ${hotel.pickup_point} (time to confirm)`;
                    $('#pickupLocation').val(pickupValue);
                    $('#hotelSearch').val(hotel.name);

                    $('#selectedHotelInfo').slideDown();
                    $('#hotelSuggestions').hide();

                    showNotification('Hotel selected! Pickup time to be confirmed', 'info');
                }
            }

            // Hide suggestions when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#hotelSearch, #hotelSuggestions').length) {
                    $('#hotelSuggestions').hide();
                }
            });

            // Load cart from localStorage
            if (localStorage.getItem('bookingCart')) {
                cart = JSON.parse(localStorage.getItem('bookingCart'));
                updateCartCount();
            }

            // Function to update price display based on selected tour
            function updatePriceDisplay() {
                if (!selectedTourId) return;

                const tourOption = $('#modalTourSelect option:selected');
                const pricingType = tourOption.data('pricing-type');

                // Store current selections before hiding
                const currentBalloonClass = $('#balloonClass').val();
                const currentScooterOption = $('#scooterOption').val();

                // Hide all dynamic sections first
                $('#balloonPricing').hide();
                $('#scooterPricing').hide();
                $('#simplePricing').hide();
                $('#tourTypeSection').hide();
                $('#timeSelectionGroup').hide();

                // Get current values
                const adults = parseInt($('#adults').val()) || 1;
                const children = parseInt($('#children').val()) || 0;

                switch (pricingType) {
                    case 'standard':
                        $('#tourTypeSection').show();

                        const tourType = $('input[name="tourType"]:checked').val();
                        let adultPrice, childPrice;

                        if (tourType === 'private') {
                            adultPrice = parseInt(tourOption.data('price-private'));
                            childPrice = parseInt(tourOption.data('child-private'));
                            $('#timeSelectionGroup').show();
                        } else {
                            adultPrice = parseInt(tourOption.data('price-group'));
                            childPrice = parseInt(tourOption.data('child-group'));
                            $('#timeSelectionGroup').hide();
                        }

                        // Calculate totals
                        const adultTotal = adults * adultPrice;
                        const childTotal = children * childPrice;

                        // Update under-input displays
                        $('#displayAdultPrice').text(adultTotal + ' MAD');
                        $('#displayChildPrice').text(childTotal + ' MAD');

                        $('#adultLabel').text('Adults');
                        $('#childLabel').text('Children');
                        $('#childGroup').show();
                        break;

                    case 'quad':
                        $('#simplePricing').show();

                        const driverPrice = parseInt(tourOption.data('price-group'));
                        const passengerPrice = parseInt(tourOption.data('child-group'));

                        const driverTotal = adults * driverPrice;
                        const passengerTotal = children * passengerPrice;

                        // Update under-input displays for Quad
                        $('#displayAdultPrice').text(driverTotal + ' MAD');
                        $('#displayChildPrice').text(passengerTotal + ' MAD');

                        $('#adultLabel').text('Number of Drivers');
                        $('#childLabel').text('Number of Passengers');
                        $('#childGroup').show();
                        break;

                    case 'balloon':
                        $('#balloonPricing').show();

                        const classic = parseInt(tourOption.data('classic'));
                        const premium = parseInt(tourOption.data('premium'));
                        const vip = parseInt(tourOption.data('vip'));
                        const royal = parseInt(tourOption.data('royal'));

                        // Create the dropdown with all options and preserve selection
                        let balloonHtml = `<select id="balloonClass" class="form-control">`;
                        balloonHtml += `<option value="classic" data-price="${classic}" ${currentBalloonClass === 'classic' ? 'selected' : ''}>Classic Flight - ${classic} MAD</option>`;
                        balloonHtml += `<option value="premium" data-price="${premium}" ${currentBalloonClass === 'premium' ? 'selected' : ''}>Premium Flight - ${premium} MAD</option>`;
                        balloonHtml += `<option value="vip" data-price="${vip}" ${currentBalloonClass === 'vip' ? 'selected' : ''}>VIP Flight - ${vip} MAD</option>`;
                        balloonHtml += `<option value="royal" data-price="${royal}" ${currentBalloonClass === 'royal' ? 'selected' : ''}>Royal Flight - ${royal} MAD</option>`;
                        balloonHtml += `</select>`;

                        $('#balloonOptions').html(balloonHtml);

                        // Get selected price (use current selection or default to classic)
                        let selectedPrice = classic;
                        if (currentBalloonClass === 'premium') selectedPrice = premium;
                        else if (currentBalloonClass === 'vip') selectedPrice = vip;
                        else if (currentBalloonClass === 'royal') selectedPrice = royal;

                        const balloonTotal = adults * selectedPrice;

                        // Update under-input display
                        $('#displayAdultPrice').text(balloonTotal + ' MAD');
                        $('#displayChildPrice').text('0 MAD');

                        $('#adultLabel').text('Number of People');
                        $('#childGroup').hide();
                        break;

                    case 'perBuggy':
                        $('#simplePricing').show();

                        const buggyPrice = parseInt(tourOption.data('price'));
                        const buggyTotal = adults * buggyPrice;

                        // Update under-input display for Buggy
                        $('#displayAdultPrice').text(buggyTotal + ' MAD');
                        $('#displayChildPrice').text('0 MAD');

                        $('#adultLabel').text('Number of Buggies');
                        $('#childGroup').hide();
                        break;

                    case 'perPerson':
                        $('#simplePricing').show();

                        const personPrice = parseInt(tourOption.data('price'));
                        const personTotal = adults * personPrice;

                        // Update under-input display for Per Person
                        $('#displayAdultPrice').text(personTotal + ' MAD');
                        $('#displayChildPrice').text('0 MAD');

                        $('#adultLabel').text('Number of People');
                        $('#childGroup').hide();
                        break;

                    case 'scooter':
                        $('#scooterPricing').show();

                        const medinaPrice = parseInt(tourOption.data('price-medina'));
                        const palmPrice = parseInt(tourOption.data('price-palm'));

                        // Create the dropdown with all options and preserve selection
                        let scooterHtml = `<select id="scooterOption" class="form-control">`;
                        scooterHtml += `<option value="medina" data-price="${medinaPrice}" ${currentScooterOption === 'medina' ? 'selected' : ''}>Medina Tour - ${medinaPrice} MAD</option>`;
                        scooterHtml += `<option value="palm" data-price="${palmPrice}" ${currentScooterOption === 'palm' ? 'selected' : ''}>Palm Grove Tour - ${palmPrice} MAD</option>`;
                        scooterHtml += `</select>`;

                        $('#scooterOptions').html(scooterHtml);

                        // Get selected price
                        let scooterPrice = medinaPrice;
                        if (currentScooterOption === 'palm') scooterPrice = palmPrice;

                        const scooterTotal = adults * scooterPrice;

                        $('#displayAdultPrice').text(scooterTotal + ' MAD');
                        $('#displayChildPrice').text('0 MAD');

                        $('#adultLabel').text('Number of Scooters');
                        $('#childGroup').hide();
                        break;

                    case 'adultChild':
                        $('#simplePricing').show();

                        const adultPriceAC = parseInt(tourOption.data('adult-price'));
                        const childPriceAC = parseInt(tourOption.data('child-price'));

                        const adultTotalAC = adults * adultPriceAC;
                        const childTotalAC = children * childPriceAC;

                        // Update under-input displays for Adult/Child
                        $('#displayAdultPrice').text(adultTotalAC + ' MAD');
                        $('#displayChildPrice').text(childTotalAC + ' MAD');

                        $('#adultLabel').text('Adults');
                        $('#childLabel').text('Children');
                        $('#childGroup').show();
                        break;
                }
            }

            // Book Now button click
            $(document).on('click', '.book-btn', function(e) {
                e.preventDefault();
                const tourCard = $(this).closest('.excursion-card');
                const tourId = tourCard.data('tour-id');

                $('#modalTourSelect').val(tourId).trigger('change');
                $('#bookingModal').fadeIn();
            });

            // Tour select change
            $('#modalTourSelect').on('change', function() {
                const tourId = $(this).val();
                if (tourId) {
                    selectedTourId = tourId;
                    $('#bookingForm').slideDown();
                    const today = new Date().toISOString().split('T')[0];
                    $('#tourDate').attr('min', today);
                    updatePriceDisplay();
                } else {
                    $('#bookingForm').slideUp();
                }
            });

            // Show/hide time selection based on tour type
            $('input[name="tourType"]').on('change', function() {
                if (selectedTourId) {
                    if ($(this).val() === 'private') {
                        $('#timeSelectionGroup').slideDown();
                    } else {
                        $('#timeSelectionGroup').slideUp();
                        $('#tourTime').val('');
                    }
                    updatePriceDisplay();
                }
            });

            // Add to Cart button
            $('#addToCartBtn').on('click', function() {
                if (!validateBookingForm()) return;

                const tourOption = $('#modalTourSelect option:selected');
                const pricingType = tourOption.data('pricing-type');
                let totalPrice = 0;
                let cartItem = {
                    id: Date.now() + Math.random(),
                    tourId: selectedTourId,
                    title: tourOption.text().split(' - ')[0],
                    date: $('#tourDate').val(),
                    pricingType: pricingType
                };

                switch (pricingType) {
                    case 'standard':
                        const tourType = $('input[name="tourType"]:checked').val();
                        const stdAdultPrice = tourType === 'private' ?
                            parseInt(tourOption.data('price-private')) :
                            parseInt(tourOption.data('price-group'));
                        const stdChildPrice = tourType === 'private' ?
                            parseInt(tourOption.data('child-private')) :
                            parseInt(tourOption.data('child-group'));

                        const adults = parseInt($('#adults').val());
                        const children = parseInt($('#children').val());

                        cartItem.adults = adults;
                        cartItem.children = children;
                        cartItem.tourType = tourType;
                        cartItem.adultPrice = stdAdultPrice;
                        cartItem.childPrice = stdChildPrice;
                        totalPrice = (adults * stdAdultPrice) + (children * stdChildPrice);
                        cartItem.time = tourType === 'private' ? $('#tourTime').val() : '09:00 (Fixed)';
                        break;

                    case 'quad':
                        const driverPrice = parseInt(tourOption.data('price-group'));
                        const passengerPrice = parseInt(tourOption.data('child-group'));
                        const drivers = parseInt($('#adults').val());
                        const passengers = parseInt($('#children').val());

                        cartItem.drivers = drivers;
                        cartItem.passengers = passengers;
                        cartItem.driverPrice = driverPrice;
                        cartItem.passengerPrice = passengerPrice;
                        totalPrice = (drivers * driverPrice) + (passengers * passengerPrice);
                        cartItem.time = '09:00 (Fixed)';
                        break;

                    case 'balloon':
                        const flightClass = $('#balloonClass').val() || 'classic';
                        let classPrice = 0;
                        switch (flightClass) {
                            case 'classic':
                                classPrice = parseInt(tourOption.data('classic'));
                                break;
                            case 'premium':
                                classPrice = parseInt(tourOption.data('premium'));
                                break;
                            case 'vip':
                                classPrice = parseInt(tourOption.data('vip'));
                                break;
                            case 'royal':
                                classPrice = parseInt(tourOption.data('royal'));
                                break;
                        }
                        const people = parseInt($('#adults').val());

                        cartItem.flightClass = flightClass;
                        cartItem.people = people;
                        cartItem.pricePerPerson = classPrice;
                        totalPrice = people * classPrice;
                        cartItem.time = 'Sunrise (exact time confirmed after booking)';
                        break;

                    case 'perBuggy':
                        const buggyPrice = parseInt(tourOption.data('price'));
                        const buggies = parseInt($('#adults').val());

                        cartItem.buggies = buggies;
                        cartItem.pricePerBuggy = buggyPrice;
                        totalPrice = buggies * buggyPrice;
                        cartItem.time = '09:00 (Fixed)';
                        break;

                    case 'perPerson':
                        const perPersonPrice = parseInt(tourOption.data('price'));
                        const persons = parseInt($('#adults').val());

                        cartItem.persons = persons;
                        cartItem.pricePerPerson = perPersonPrice;
                        totalPrice = persons * perPersonPrice;
                        cartItem.time = '09:00 (Fixed)';
                        break;

                    case 'scooter':
                        const scooterOption = $('#scooterOption').val() || 'medina';
                        const scooterPrice = 450;
                        const scooters = parseInt($('#adults').val());

                        cartItem.scooterOption = scooterOption;
                        cartItem.scooters = scooters;
                        cartItem.pricePerScooter = scooterPrice;
                        totalPrice = scooters * scooterPrice;
                        cartItem.time = '09:00 (Fixed)';
                        break;

                    case 'adultChild':
                        const acAdultPrice = parseInt(tourOption.data('adult-price'));
                        const acChildPrice = parseInt(tourOption.data('child-price'));
                        const adultsCount = parseInt($('#adults').val());
                        const childrenCount = parseInt($('#children').val());

                        cartItem.adults = adultsCount;
                        cartItem.children = childrenCount;
                        cartItem.adultPrice = acAdultPrice;
                        cartItem.childPrice = acChildPrice;
                        totalPrice = (adultsCount * acAdultPrice) + (childrenCount * acChildPrice);
                        cartItem.time = '09:00 (Fixed)';
                        break;
                }

                cartItem.totalPrice = totalPrice;
                cart.push(cartItem);
                saveCart();
                updateCartDisplay();

                $('#bookingForm').slideUp();
                $('#modalTourSelect').val('');
                $('#tourDate, #tourTime, #specialRequests').val('');
                $('#adults').val('1');
                $('#children').val('0');
                if ($('#balloonClass').length) $('#balloonClass').val('classic');
                if ($('#scooterOption').length) $('#scooterOption').val('medina');
                $('input[name="tourType"][value="group"]').prop('checked', true);
                $('#timeSelectionGroup').slideUp();

                showNotification('Tour added to cart!', 'success');
            });

            // Remove from cart
            $(document).on('click', '.remove-item', function() {
                const itemId = $(this).data('id');
                cart = cart.filter(item => item.id != itemId);
                saveCart();
                updateCartDisplay();
                showNotification('Item removed from cart', 'info');
            });

            // Update cart display
            function updateCartDisplay() {
                const cartContainer = $('#cartItems');
                if (cart.length === 0) {
                    cartContainer.html('<div class="empty-cart"><i class="fas fa-shopping-cart"></i><p>Your cart is empty</p></div>');
                    $('#subtotal').text('0 MAD');
                    $('#totalAmount').text('0 MAD');
                } else {
                    let html = '';
                    let subtotal = 0;

                    cart.forEach(item => {
                        subtotal += item.totalPrice;

                        // Format time display
                        const timeDisplay = item.time === '09:00 (Fixed)' ? '9:00 AM (Fixed)' : item.time;

                        // Handle different pricing types for display
                        let peopleDisplay = '';
                        let tourTypeDisplay = '';

                        switch (item.pricingType) {
                            case 'standard':
                                peopleDisplay = `${item.adults || 0} Adults, ${item.children || 0} Children`;
                                tourTypeDisplay = item.tourType || 'group';
                                break;

                            case 'quad':
                                peopleDisplay = `${item.drivers || 0} Drivers, ${item.passengers || 0} Passengers`;
                                tourTypeDisplay = 'quad';
                                break;

                            case 'balloon':
                                peopleDisplay = `${item.people || 0} People`;
                                tourTypeDisplay = item.flightClass ? item.flightClass + ' flight' : 'balloon';
                                break;

                            case 'perBuggy':
                                peopleDisplay = `${item.buggies || 0} Buggies`;
                                tourTypeDisplay = 'buggy';
                                break;

                            case 'perPerson':
                                peopleDisplay = `${item.persons || 0} Persons`;
                                tourTypeDisplay = 'per person';
                                break;

                            case 'scooter':
                                peopleDisplay = `${item.scooters || 0} Scooters (${item.scooterOption || 'medina'} tour)`;
                                tourTypeDisplay = 'scooter';
                                break;

                            case 'adultChild':
                                peopleDisplay = `${item.adults || 0} Adults, ${item.children || 0} Children`;
                                tourTypeDisplay = 'tour';
                                break;

                            default:
                                peopleDisplay = `${item.adults || 0} Adults, ${item.children || 0} Children`;
                                tourTypeDisplay = 'tour';
                        }

                        html += `
                            <div class="cart-item">
                                <button class="remove-item" data-id="${item.id}"><i class="fas fa-times"></i></button>
                                <div class="cart-item-header">
                                    <span class="cart-item-title">${item.title}</span>
                                    <span class="cart-item-price">${item.totalPrice} MAD</span>
                                </div>
                                <div class="cart-item-details">
                                    <span><i class="fas fa-calendar"></i> ${item.date}</span>
                                    <span><i class="fas fa-clock"></i> ${timeDisplay}</span>
                                    <span><i class="fas fa-users"></i> ${peopleDisplay}</span>
                                    <span><i class="fas fa-tag"></i> ${tourTypeDisplay}</span>
                                </div>
                            </div>
                        `;
                    });

                    cartContainer.html(html);
                    $('#subtotal').text(subtotal + ' MAD');
                    $('#totalAmount').text(subtotal + ' MAD');
                }

                updateCartCount();
            }

            function updateCartCount() {
                const count = cart.length;
                $('#cartCount').text(count);
                if (count > 0) {
                    $('#cartIcon').fadeIn();
                } else {
                    $('#cartIcon').fadeOut();
                }
            }

            function saveCart() {
                localStorage.setItem('bookingCart', JSON.stringify(cart));
            }

            function validateBookingForm() {
                if (!$('#tourDate').val()) {
                    showNotification('Please select a date', 'error');
                    return false;
                }

                const tourType = $('input[name="tourType"]:checked').val();
                if (tourType === 'private' && !$('#tourTime').val()) {
                    showNotification('Please select a preferred time for your private tour', 'error');
                    return false;
                }

                if (!$('#adults').val() || parseInt($('#adults').val()) < 1) {
                    showNotification('Please add at least 1 adult', 'error');
                    return false;
                }
                return true;
            }

            function showNotification(message, type) {
                const notification = $(`<div class="notification ${type}">${message}</div>`);
                $('body').append(notification);
                setTimeout(() => notification.fadeOut(() => notification.remove()), 3000);
            }

            // Cart icon click
            $('#cartIcon').on('click', function() {
                $('#bookingModal').fadeIn();
            });

            // Close modal
            $('.close-modal, #continueShoppingBtn').on('click', function() {
                $('#bookingModal').fadeOut();
            });

            // Submit booking
            $('#submitBookingBtn').on('click', function() {
                if (!validateCustomerInfo()) return;
                if (cart.length === 0) {
                    showNotification('Your cart is empty', 'error');
                    return;
                }

                const bookingData = {
                    customer: {
                        name: $('#customerName').val(),
                        email: $('#customerEmail').val(),
                        phone: $('#customerPhone').val(),
                        pickup: $('#pickupLocation').val()
                    },
                    items: cart,
                    total: cart.reduce((sum, item) => sum + item.totalPrice, 0),
                    date: new Date().toISOString(),
                    status: 'pending'
                };

                $.ajax({
                    url: 'process-booking.php',
                    method: 'POST',
                    data: {
                        booking: JSON.stringify(bookingData)
                    },
                    success: function(response) {
                        cart = [];
                        saveCart();
                        updateCartDisplay();
                        alert('Booking confirmed! We will contact you shortly on WhatsApp.');
                        $('#bookingModal').fadeOut();
                        $('#customerName, #customerEmail, #customerPhone').val('');
                        $('#hotelSearch').val('Your Marrakech Riad/Hotel');
                        $('#pickupLocation').val('Your Marrakech Riad/Hotel');
                        $('#selectedHotelInfo').hide();
                    },
                    error: function() {
                        showNotification('Error processing booking. Please try again.', 'error');
                    }
                });
            });

            function validateCustomerInfo() {
                if (!$('#customerName').val()) {
                    showNotification('Please enter your name', 'error');
                    return false;
                }
                if (!$('#customerEmail').val() || !$('#customerEmail').val().includes('@')) {
                    showNotification('Please enter a valid email', 'error');
                    return false;
                }
                if (!$('#customerPhone').val()) {
                    showNotification('Please enter your phone number', 'error');
                    return false;
                }

                // Check if hotel field has ANY value (not empty)
                const hotelValue = $('#hotelSearch').val();
                if (!hotelValue || hotelValue.trim() === '') {
                    showNotification('Please enter your hotel name', 'error');
                    return false;
                }

                // If no pickup location is set, use what user typed
                if (!$('#pickupLocation').val()) {
                    $('#pickupLocation').val(hotelValue);
                }

                return true;
            }

            // Filter button clicks
            $('.filter-btn').click(function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                currentFilter = $(this).data('filter');
                filterAndDisplayTours();
            });

            // Search input
            $('#searchInput').on('input', function() {
                currentSearch = $(this).val();
                filterAndDisplayTours();
            });

            // Clear search
            $('#clearSearch').click(function() {
                $('#searchInput').val('');
                currentSearch = '';
                filterAndDisplayTours();
            });

            // Load tours on page load
            loadTours();
            updateCartDisplay();

            // ====== FIXED EVENT LISTENERS FOR PRICE UPDATE ======
            $('#adults, #children').on('input change', function() {
                if (selectedTourId) {
                    updatePriceDisplay();
                }
            });

            // Use delegated event listeners for dynamically created elements
            $(document).on('change', '#balloonClass', function() {
                if (selectedTourId) {
                    updatePriceDisplay();
                }
            });

            $(document).on('change', '#scooterOption', function() {
                if (selectedTourId) {
                    updatePriceDisplay();
                }
            });
        });
    </script>

    <!-- Add notification styles -->
    <style>
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 10000;
            animation: slideInRight 0.3s ease;
        }

        .notification.success {
            background: #28a745;
        }

        .notification.error {
            background: #dc3545;
        }

        .notification.info {
            background: #17a2b8;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .empty-cart {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .empty-cart i {
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>

</body>

<footer>
    <?php include '../includes/footer.php'; ?>
</footer>

</html>
<?php
ob_end_flush();
?>