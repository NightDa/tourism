<?php
// Start output buffering for potential caching later
ob_start();

// Include analytics tracker
require_once '../includes/analytics.php';

// Get featured tours from excursions data
$data_file = __DIR__ . '/../data/excursions.json';
if (file_exists($data_file)) {
  $json_data = file_get_contents($data_file);
  $data = json_decode($json_data, true);
  $all_tours = $data['excursions'] ?? [];
  // Get first 3 tours for featured section
  $featured_tours = array_slice($all_tours, 0, 3);
} else {
  $featured_tours = [];
}

// Helper function to get clean price for labels
function getSimplePrice($tour)
{
  $price = $tour['priceTag'] ?? '';
  // Extract numbers followed by MAD or dh
  if (preg_match('/(\d+)\s*(MAD|dh)/i', $price, $matches)) {
    return $matches[1] . ' MAD';
  }
  // If no match, return first number found + MAD
  if (preg_match('/(\d+)/', $price, $matches)) {
    return $matches[1] . ' MAD';
  }
  return 'Price on request';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- SEO Meta Tags -->
  <meta name="description" content="Discover the magic of Morocco with Travol Morocco. Expert guided tours, excursions, and unforgettable experiences in Marrakech, Sahara Desert, and beyond.">
  <meta name="keywords" content="Morocco tours, Marrakech excursions, Sahara desert tours, Atlas mountains, Moroccan travel agency">
  <meta name="robots" content="index, follow">
  <meta name="author" content="Travol Morocco">

  <!-- Open Graph for Social Media -->
  <meta property="og:title" content="Travol Morocco - Discover Morocco">
  <meta property="og:description" content="Professional Moroccan tour agency offering unforgettable experiences">
  <meta property="og:image" content="../assets/img/logo.png">
  <meta property="og:type" content="website">
  <meta property="og:url" content="http://localhost:8000/pages/index.php">

  <title>Travol Morocco - Discover Morocco</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link rel="stylesheet" href="../assets/css/responsive.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <!-- Admin link for logged in users (will be hidden by CSS) -->
  <?php
  session_start();
  if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
    <style>
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

      .admin-quick-link i {
        margin-right: 5px;
      }
    </style>
  <?php endif; ?>
</head>

<body id="page-content">
  <!-- ========== Preloader Start ========== -->
  <div id="preloader">
    <div class="sand-layer"></div>
    <div class="loader-content">
      <img src="../assets/img/Sans titre.png" alt="Travol Morocco Logo" class="preload-logo" />
      <h2 class="loading-text">Discover Morocco...</h2>
    </div>
  </div>
  <!-- ========== Preloader End ========== -->

  <!-- ===========header ==================start-->
  <header>
    <div class="container">
      <nav>
        <div class="logo">
          <img src="../assets/img/Sans titre.png" alt="Travol Morocco Logo" />
        </div>
        <ul>
          <div class="btn">
            <i class="fas fa-times close-btn"></i>
          </div>
          <li><a href="index.php" class="active">Home</a></li>
          <li><a href="about.php">About</a></li>
          <li><a href="excursions.php">Tours</a></li>
          <li><a href="contact.php">Contact</a></li>
        </ul>
        <div class="btn">
          <i class="fas fa-bars menu-btn"></i>
        </div>
      </nav>
    </div>
  </header>
  <!-- ===========header ==================Close-->

  <!-- Admin Quick Link (only visible when logged in) -->
  <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
    <a href="../admin/index.php" class="admin-quick-link">
      <i class="fas fa-cog"></i> Admin Dashboard
    </a>
  <?php endif; ?>

  <!-- ===========Showcase ==================Start-->
  <main>
    <div class="slide-container swiper">
      <div class="slide-content swiper-wrapper">
        <div class="overlay swiper-slide">
          <img src="../assets/img/show-case-1.jpg" alt="Showcase Morocco Image 1" />
          <div class="img-overlay">
            <p>Let's Travel Around Morocco With Us</p>
            <h2><span>Discover</span> Morocco</h2>
            <h2>With Our Guide</h2>
          </div>
        </div>
        <div class="overlay swiper-slide">
          <img src="../assets/img/show-case-2.jfif" alt="Showcase Morocco Image 2" />
          <div class="img-overlay">
            <p>Let's Travel Around Morocco With Us</p>
            <h2><span>Discover</span> Morocco</h2>
            <h2>With Our Guide</h2>
          </div>
        </div>
        <div class="overlay swiper-slide">
          <img src="../assets/img/show-case-3.jpg" alt="Showcase Morocco Image 3" />
          <div class="img-overlay">
            <p>Let's Travel Around Morocco With Us</p>
            <h2>Discover Morocco</h2>
            <h2>With Our <span>Travol</span></h2>
          </div>
        </div>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </main>
  <!-- =========== Showcase ==================Close-->

  <!-- =========== Search Location ==================Start-->
  <section id="location-search">
    <div class="container">
      <div class="form-wrapper">
        <form id="travel-search-form" action="excursions.php" method="GET">
          <!-- Destination -->
          <div class="form-group">
            <label for="destination">
              <i class="fas fa-map-marker-alt"></i> Where to?
            </label>
            <input type="text" id="destination" name="search" placeholder="Enter city or destination" class="form-control"
              list="cities" />
            <datalist id="cities">
              <option value="Marrakech">Marrakech, Morocco</option>
              <option value="Casablanca">Casablanca, Morocco</option>
              <option value="Agadir">Agadir, Morocco</option>
              <option value="Tangier">Tangier, Morocco</option>
              <option value="Rabat">Rabat, Morocco</option>
              <option value="Fez">Fez, Morocco</option>
              <option value="Chefchaouen">Chefchaouen, Morocco</option>
              <option value="Essaouira">Essaouira, Morocco</option>
              <option value="Sahara Desert">Sahara Desert, Morocco</option>
              <option value="Atlas Mountains">Atlas Mountains, Morocco</option>
            </datalist>
          </div>

          <!-- Trip Type -->
          <div class="form-group">
            <label for="trip-type">
              <i class="fas fa-suitcase"></i> What type?
            </label>
            <select class="form-control" id="trip-type" name="type">
              <option value="tour">Tour/Excursion</option>
              <option value="flight">Flight</option>
              <option value="hotel">Hotel</option>
              <option value="package">Complete Package</option>
            </select>
          </div>

          <!-- Duration -->
          <div class="form-group">
            <label for="duration">
              <i class="fas fa-calendar-alt"></i> Duration
            </label>
            <select class="form-control" id="duration" name="duration">
              <option value="1">1 Day</option>
              <option value="2-4">2-4 Days</option>
              <option value="5-7">5-7 Days</option>
              <option value="7+">7+ Days</option>
            </select>
          </div>

          <!-- Travelers -->
          <div class="form-group">
            <label for="travelers">
              <i class="fas fa-users"></i> Travelers
            </label>
            <select class="form-control" id="travelers" name="travelers">
              <option value="1">1 Person</option>
              <option value="2" selected>2 People</option>
              <option value="3">3 People</option>
              <option value="4">4 People</option>
              <option value="5+">5+ People</option>
            </select>
          </div>

          <!-- Search Button -->
          <div class="form-group search-btn-group">
            <button type="submit" class="primary-btn" id="search-btn">
              <i class="fas fa-search"></i> Search & Book
            </button>
          </div>
        </form>

        <!-- Quick Links -->
        <div class="quick-links">
          <p>Quick Book:</p>
          <div class="quick-links-grid">
            <a href="excursions.php" class="quick-link-btn">
              <i class="fas fa-hiking"></i> Day Tours
            </a>
            <a href="#" class="quick-link-btn" onclick="bookFlight('Marrakech')">
              <i class="fas fa-plane"></i> Flights
            </a>
            <a href="#" class="quick-link-btn" onclick="bookHotel('Marrakech')">
              <i class="fas fa-hotel"></i> Hotels
            </a>
            <a href="#" class="quick-link-btn" onclick="bookTour('Sahara')">
              <i class="fas fa-camel"></i> Desert Tours
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- =========== Search Location ==================Close-->

  <!-- =========== About ==================Start-->
  <section id="about">
    <div class="container">
      <div class="about-content-wrapper">
        <div class="agency-left-side">
          <p class="heading-normal-txt">
            EXPERIENCE MOROCCO LIKE NEVER BEFORE
          </p>
          <h2 class="headings">
            DISCOVER THE <span>MAGIC</span> OF MOROCCO WITH OUR
            <span>EXPERT GUIDES</span>
          </h2>
          <p class="lead">
            From the vibrant souks of Marrakech to the golden dunes of the
            Sahara, our agency offers immersive experiences that bring
            Morocco's rich culture and natural beauty to life.
          </p>
          <br />
          <p class="lead">
            With over 20 years of experience, we specialize in creating
            unforgettable journeys that combine authentic cultural immersion
            with modern comfort and convenience.
          </p>
          <ul>
            <li>
              <div class="icons">
                <i class="fa fa-check"></i>
                <p>Over 20 Years of Local Expertise</p>
              </div>
            </li>
            <li>
              <div class="icons">
                <i class="fa fa-check"></i>
                <p>150+ Curated Moroccan Destinations</p>
              </div>
            </li>
            <li>
              <div class="icons">
                <i class="fa fa-phone-volume"></i>
                <p>Call Us: +212 655 23 71 96</p>
              </div>
            </li>
            <li>
              <div class="icons">
                <i class="fa fa-envelope"></i>
                <p>Email: info@travolmorocco.com</p>
              </div>
            </li>
          </ul>
          <a href="excursions.php" class="primary-btn view-tours-btn">
            View All Tours <i class="fa fa-arrow-right"></i>
          </a>
        </div>
        <div class="agency-right-side">
          <div class="img">
            <img src="../assets/img/Northern+Morocco.jfif" alt="Morocco Landscape" />
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- =========== About ==================Close-->

  <!-- =========== Choose your place ==================Start-->
  <section id="choose-place">
    <div class="container">
      <p class="heading-normal-txt">POPULAR DESTINATIONS</p>
      <h2 class="headings">TOP <span>CITIES</span> TO VISIT</h2>
      <div class="choose-wrapper">
        <div class="img-left-side">
          <div class="lg-img">
            <img src="../assets/img/_Best-Restaurants-Marrakesh-Comptoir-Darna.webp" alt="Marrakech" />
            <div class="img-content">
              <h2>Marrakech Tours</h2>
              <div class="hidden-content">
                <span><i class="fa fa-clock"></i> 3-10 days</span>
                <span><i class="fa fa-user"></i> 2-12+</span>
                <span><i class="fa fa-location-dot"></i> Marrakech</span>
              </div>
            </div>
          </div>
        </div>
        <div class="img-right-side">
          <div class="lg-img">
            <img src="../assets/img/15-days-tour-from-Marrakech-marrakech-700x400.webp" alt="Desert Tour" />
            <div class="img-content">
              <h2>Sahara Desert</h2>
              <div class="hidden-content">
                <span><i class="fa fa-clock"></i> 3-5 days</span>
                <span><i class="fa fa-user"></i> 2-8+</span>
                <span><i class="fa fa-location-dot"></i> Sahara</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- row wise images -->
      <div class="row-wise-img">
        <div class="lg-img">
          <img src="../assets/img/casablanca-mosque.jpg" alt="Casablanca" />
          <div class="img-content">
            <h2>Casablanca</h2>
            <div class="hidden-content">
              <span><i class="fa fa-clock"></i> 1-3 days</span>
              <span><i class="fa fa-user"></i> 2-10+</span>
              <span><i class="fa fa-location-dot"></i> Casablanca</span>
            </div>
          </div>
        </div>

        <div class="lg-img">
          <img src="../assets/img/morocco-tangier.jpg" alt="Tangier" />
          <div class="img-content">
            <h2>Tangier</h2>
            <div class="hidden-content">
              <span><i class="fa fa-clock"></i> 2-4 days</span>
              <span><i class="fa fa-user"></i> 2-8+</span>
              <span><i class="fa fa-location-dot"></i> Tangier</span>
            </div>
          </div>
        </div>

        <div class="lg-img">
          <img src="../assets/img/Rabat-1-scaled.webp" alt="Rabat" />
          <div class="img-content">
            <h2>Rabat</h2>
            <div class="hidden-content">
              <span><i class="fa fa-clock"></i> 1-3 days</span>
              <span><i class="fa fa-user"></i> 2-6+</span>
              <span><i class="fa fa-location-dot"></i> Rabat</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- =========== Choose your place ==================Close-->

  <!-- =========== Featured Tours ==================Start-->
  <section id="featured-tours">
    <div class="container">
      <p class="heading-normal-txt">FEATURED TOURS</p>
      <h2 class="headings">POPULAR <span>EXCURSIONS</span></h2>
      <p class="lead tours-intro">
        Discover our most popular day trips and excursions from Marrakech
      </p>

      <div class="tours-grid">
        <?php if (!empty($featured_tours)): ?>
          <?php foreach ($featured_tours as $tour): ?>
            <div class="tour-card">
              <div class="tour-img">
                <img src="<?php echo htmlspecialchars($tour['image']); ?>" alt="<?php echo htmlspecialchars($tour['title']); ?>" onerror="this.src='../assets/img/placeholder.jpg'" />
              </div>
              <div class="tour-content">
                <h3><?php echo htmlspecialchars($tour['title']); ?></h3>
                <div class="tour-details">
                  <span><i class="fa fa-clock"></i> <?php echo htmlspecialchars($tour['duration']); ?></span>
                  <span><i class="fa fa-map-marker-alt"></i> <?php echo htmlspecialchars($tour['location']); ?></span>
                </div>
                <p>
                  <?php echo htmlspecialchars(substr($tour['description'], 0, 100)) . '...'; ?>
                </p>
                <div class="tour-footer">
                  <span class="tour-price">
                    <?php echo htmlspecialchars($tour['priceTag']); ?>
                  </span>
                  <a href="excursions.php" class="tour-link">
                    Details <i class="fa fa-arrow-right"></i>
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- Fallback static tours if JSON not loaded -->
          <div class="tour-card">
            <div class="tour-img">
              <img src="https://i.ibb.co/vCK30F8t/Medina-Tour.jpg" alt="Medina Tour" />
            </div>
            <div class="tour-content">
              <h3>Medina Tour</h3>
              <div class="tour-details">
                <span><i class="fa fa-clock"></i> Full Day</span>
                <span><i class="fa fa-map-marker-alt"></i> Marrakech</span>
              </div>
              <p>Explore iconic monuments and wander through winding alleys of the Medina.</p>
              <div class="tour-footer">
                <span class="tour-price">From 350 MAD</span>
                <a href="excursions.php" class="tour-link">Details <i class="fa fa-arrow-right"></i></a>
              </div>
            </div>
          </div>
          <div class="tour-card">
            <div class="tour-img">
              <img src="https://i.ibb.co/BHdLd3Gh/Ourika-Valley.jpg" alt="Ourika Valley" />
            </div>
            <div class="tour-content">
              <h3>Ourika Valley</h3>
              <div class="tour-details">
                <span><i class="fa fa-clock"></i> Full Day</span>
                <span><i class="fa fa-map-marker-alt"></i> Atlas Mountains</span>
              </div>
              <p>Spectacular landscapes, Berber villages, and seven waterfalls.</p>
              <div class="tour-footer">
                <span class="tour-price">From 350 MAD</span>
                <a href="excursions.php" class="tour-link">Details <i class="fa fa-arrow-right"></i></a>
              </div>
            </div>
          </div>
          <div class="tour-card">
            <div class="tour-img">
              <img src="https://i.ibb.co/cWN5ZMD/Quad-Biking-in-Agafay-Desert.jpg" alt="Quad Biking" />
            </div>
            <div class="tour-content">
              <h3>Quad Biking</h3>
              <div class="tour-details">
                <span><i class="fa fa-clock"></i> Half Day</span>
                <span><i class="fa fa-map-marker-alt"></i> Agafay Desert</span>
              </div>
              <p>Exhilarating desert adventure with Berber culture immersion.</p>
              <div class="tour-footer">
                <span class="tour-price">550 MAD</span>
                <a href="excursions.php" class="tour-link">Details <i class="fa fa-arrow-right"></i></a>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div class="text-center">
        <a href="excursions.php" class="primary-btn all-tours-btn">
          All Tours <i class="fa fa-arrow-right"></i>
        </a>
      </div>
    </div>
  </section>
  <!-- =========== Featured Tours ==================Close-->

  <!-- =========== Static Counter Numbers ==================Start-->
  <section id="static-counters">
    <div class="container">
      <div class="static-wrapper">
        <div class="static-icons">
          <i class="fa fa-plane-departure"></i>
          <p class="numbers" data-ceil="850">850</p>
          <p class="txt">Flight Bookings</p>
        </div>
        <div class="static-icons">
          <i class="fa fa-home"></i>
          <p class="numbers" data-ceil="320">320</p>
          <p class="txt">Amazing Tours</p>
        </div>
        <div class="static-icons">
          <i class="fa fa-users"></i>
          <p class="numbers" data-ceil="1500">1500</p>
          <p class="txt">Happy Travelers</p>
        </div>
        <div class="static-icons">
          <i class="fa fa-star"></i>
          <p class="numbers" data-ceil="98">98</p>
          <p class="txt">% Satisfaction</p>
        </div>
      </div>
    </div>
  </section>
  <!-- =========== Static Counter Numbers ==================Close-->

  <!-- =========== Top Destination ==================Start-->
  <section id="top-destinations">
    <div class="container">
      <p class="heading-normal-txt">MUST VISIT</p>
      <h2 class="headings">TOP <span>DESTINATIONS</span></h2>
      <div class="top-destination-wrapper">
        <div class="destination-swiper swiper2">
          <div class="swiper-wrapper">
            <div class="carousel swiper-slide">
              <img src="../assets/img/Marrakech-jamaa-lafna.webp" alt="Jemaa el-Fnaa" />
              <div class="carousel-img-overlay">
                <div class="img-content">
                  <h2><i class="fa fa-location-dot"></i> JEMAA EL-FNAA</h2>
                  <div class="hidden-content-carousel">
                    <span>6 tours packages</span>
                    <a href="javascript:void(0)" onclick="alert('Coming soon!')">
                      <span>Explore Now</span><i class="fa fa-arrow-right"></i>
                    </a>
                  </div>
                </div>
              </div>
              <div class="price-label" data-label="popular">
                <p>Popular</p>
              </div>
            </div>
            <div class="carousel swiper-slide">
              <img src="../assets/img/sahara.jfif" alt="Sahara Desert" />
              <div class="carousel-img-overlay">
                <div class="img-content">
                  <h2><i class="fa fa-location-dot"></i> SAHARA DESERT</h2>
                  <div class="hidden-content-carousel">
                    <span>5 tours packages</span>
                    <a href="javascript:void(0)" onclick="alert('Coming soon!')">
                      <span>Explore Now</span><i class="fa fa-arrow-right"></i>
                    </a>
                  </div>
                </div>
              </div>
              <div class="price-label" data-label="best-seller">
                <p>Best Seller</p>
              </div>
            </div>
            <div class="carousel swiper-slide">
              <img src="../assets/img/chefchaouen.jpg" alt="Chefchaouen" />
              <div class="carousel-img-overlay">
                <div class="img-content">
                  <h2><i class="fa fa-location-dot"></i> CHEFCHAOUEN</h2>
                  <div class="hidden-content-carousel">
                    <span>4 tours packages</span>
                    <a href="javascript:void(0)" onclick="alert('Coming soon!')">
                      <span>Explore Now</span><i class="fa fa-arrow-right"></i>
                    </a>
                  </div>
                </div>
              </div>
              <div class="price-label" data-label="photogenic">
                <p>Photogenic</p>
              </div>
            </div>
            <div class="carousel swiper-slide">
              <img src="../assets/img/agadir.jpg" alt="Agadir" />
              <div class="carousel-img-overlay">
                <div class="img-content">
                  <h2><i class="fa fa-location-dot"></i> AGADIR</h2>
                  <div class="hidden-content-carousel">
                    <span>8 tours packages</span>
                    <a href="javascript:void(0)" onclick="alert('Coming soon!')">
                      <span>Explore Now</span><i class="fa fa-arrow-right"></i>
                    </a>
                  </div>
                </div>
              </div>
              <div class="price-label" data-label="beach">
                <p>Beach</p>
              </div>
            </div>
            <div class="carousel swiper-slide">
              <img src="../assets/img/meknes.jpg" alt="Meknes" />
              <div class="carousel-img-overlay">
                <div class="img-content">
                  <h2><i class="fa fa-location-dot"></i> MEKNES</h2>
                  <div class="hidden-content-carousel">
                    <span>3 tours packages</span>
                    <a href="javascript:void(0)" onclick="alert('Coming soon!')">
                      <span>Explore Now</span><i class="fa fa-arrow-right"></i>
                    </a>
                  </div>
                </div>
              </div>
              <div class="price-label" data-label="historical">
                <p>Historical</p>
              </div>
            </div>
            <div class="carousel swiper-slide">
              <img src="../assets/img/essaouira-beach.jpg" alt="Essaouira" />
              <div class="carousel-img-overlay">
                <div class="img-content">
                  <h2><i class="fa fa-location-dot"></i> ESSAOUIRA</h2>
                  <div class="hidden-content-carousel">
                    <span>5 tours packages</span>
                    <a href="javascript:void(0)" onclick="alert('Coming soon!')">
                      <span>Explore Now</span><i class="fa fa-arrow-right"></i>
                    </a>
                  </div>
                </div>
              </div>
              <div class="price-label" data-label="coastal">
                <p>Coastal</p>
              </div>
            </div>
          </div>
        </div>
        <!-- Swiper Navigation -->

        <div class="swiper-pagination swiper2-pagination"></div>
      </div>
    </div>
  </section>
  <!-- =========== Top Destination ==================Close-->

  <!-- =========== Video background ==================Start-->
  <section id="costa-victoria">
    <div class="video-bg">
      <iframe
        src="https://www.youtube.com/embed/QipIufCH3rw?autoplay=1&mute=1&loop=1&playlist=QipIufCH3rw&controls=0&showinfo=0&modestbranding=1&rel=0"
        frameborder="0" allow="autoplay; fullscreen" allowfullscreen>
      </iframe>
    </div>

    <div class="video-content">
      <h2>Experience Morocco</h2>
      <span><i class="fa fa-clock"></i> 3-14 Days</span>
      <span><i class="fa fa-user"></i> 2-20 People</span>
      <span><i class="fa fa-map-marker"></i> All Morocco</span>
      <div class="video-btn-container">
        <a href="contact.php" class="primary-btn video-book-btn">
          Book Your Trip <i class="fa fa-arrow-right"></i>
        </a>
      </div>
    </div>
  </section>
  <!-- =========== Video background ==================Close-->

  <!-- =========== Most Popular Excursions ==================Start-->
  <section id="travel-citys">
    <div class="container">
      <p class="heading-normal-txt">MOST POPULAR</p>
      <h2 class="headings">EXCURSIONS <span>& TOURS</span></h2>

      <?php
      // Get all excursions (already loaded in $all_tours)
      $all_excursions = $all_tours ?? [];

      // Take first 8 excursions for this section
      $featured_excursions = array_slice($all_excursions, 0, 8);

      // First block: first 4 excursions
      $block1_excursions = array_slice($featured_excursions, 0, 4);
      // Second block: next 4 excursions
      $block2_excursions = array_slice($featured_excursions, 4, 4);
      ?>

      <!-- First Excursion Block -->
      <div class="travel-citys-wrapper">
        <?php if (!empty($block1_excursions)):
          $first = $block1_excursions[0];
        ?>
          <div class="citys-content">
            <h2 class="secondary-headings"><?php echo htmlspecialchars($first['title']); ?></h2>
            <p class="lead">
              <?php echo htmlspecialchars(substr($first['description'], 0, 180)) . '...'; ?>
            </p>
            <ul>
              <div class="citys-famous-location">
                <li><i class="fa fa-clock"></i> <?php echo htmlspecialchars($first['duration']); ?></li>
                <li><i class="fa fa-map-marker-alt"></i> <?php echo htmlspecialchars($first['location']); ?></li>
                <li><i class="fa fa-tag"></i> <?php echo htmlspecialchars($first['priceTag']); ?></li>
              </div>
            </ul>
            <a href="excursions.php" class="primary-btn">
              View Details <i class="fa fa-arrow-right"></i>
            </a>
          </div>
        <?php endif; ?>

        <div class="slider-content-wrapper">
          <div class="city-swiper swiper3">
            <div class="swiper-wrapper">
              <?php foreach ($block1_excursions as $exc): ?>
                <div class="carousel swiper-slide">
                  <img src="<?php echo htmlspecialchars($exc['image']); ?>"
                    alt="<?php echo htmlspecialchars($exc['title']); ?>"
                    onerror="this.src='../assets/img/placeholder.jpg'">
                  <div class="carousel-img-overlay">
                    <div class="img-content">
                      <h2><i class="fa fa-location-dot"></i> <?php echo htmlspecialchars($exc['title']); ?></h2>
                      <div class="hidden-content-carousel">
                        <span><i class="fa fa-clock"></i> <?php echo htmlspecialchars($exc['duration']); ?></span>
                        <span><i class="fa fa-user"></i> <?php echo htmlspecialchars($exc['location']); ?></span>
                        <span><i class="fa fa-tag"></i> <?php echo htmlspecialchars(getSimplePrice($exc)); ?></span>
                      </div>
                    </div>
                  </div>
                  <div class="price-label">
                    <p><?php echo htmlspecialchars(getSimplePrice($exc)); ?></p>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
      <!-- First Excursion Block close -->

      <!-- Second Excursion Block -->
      <?php if (!empty($block2_excursions)):
        $first2 = $block2_excursions[0];
      ?>
        <div class="rabat-content">
          <div class="slider-content-wrapper">
            <div class="city-swiper swiper3">
              <div class="swiper-wrapper">
                <?php foreach ($block2_excursions as $exc): ?>
                  <div class="carousel swiper-slide">
                    <img src="<?php echo htmlspecialchars($exc['image']); ?>"
                      alt="<?php echo htmlspecialchars($exc['title']); ?>"
                      onerror="this.src='../assets/img/placeholder.jpg'">
                    <div class="carousel-img-overlay">
                      <div class="img-content">
                        <h2><i class="fa fa-location-dot"></i> <?php echo htmlspecialchars($exc['title']); ?></h2>
                        <div class="hidden-content-carousel">
                          <span><i class="fa fa-clock"></i> <?php echo htmlspecialchars($exc['duration']); ?></span>
                          <span><i class="fa fa-user"></i> <?php echo htmlspecialchars($exc['location']); ?></span>
                          <span><i class="fa fa-tag"></i> <?php echo htmlspecialchars(getSimplePrice($exc)); ?></span>
                        </div>
                      </div>
                    </div>
                    <div class="price-label">
                      <p><?php echo htmlspecialchars(getSimplePrice($exc)); ?></p>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <div class="citys-content">
            <h2 class="secondary-headings"><?php echo htmlspecialchars($first2['title']); ?></h2>
            <p class="lead">
              <?php echo htmlspecialchars(substr($first2['description'], 0, 180)) . '...'; ?>
            </p>
            <ul>
              <div class="citys-famous-location">
                <li><i class="fa fa-clock"></i> <?php echo htmlspecialchars($first2['duration']); ?></li>
                <li><i class="fa fa-map-marker-alt"></i> <?php echo htmlspecialchars($first2['location']); ?></li>
                <li><i class="fa fa-tag"></i> <?php echo htmlspecialchars($first2['priceTag']); ?></li>
              </div>
            </ul>
            <a href="excursions.php" class="primary-btn">
              View Details <i class="fa fa-arrow-right"></i>
            </a>
          </div>
        </div>
      <?php endif; ?>

      <?php if (empty($block1_excursions) && empty($block2_excursions)): ?>
        <p class="text-center" style="padding: 50px;">No excursions available at the moment.</p>
      <?php endif; ?>
    </div>
  </section>
  <!-- =========== Most Popular Excursions ==================Close-->

  <!-- =========== Call to Action ==================Start-->
  <section id="call-to-action">
    <div class="container">
      <h2>Ready for Your Moroccan Adventure?</h2>
      <p>
        Contact us today to start planning your dream trip to Morocco
      </p>
      <div class="cta-buttons">
        <a href="contact.php" class="primary-btn cta-contact-btn">
          Contact Us <i class="fa fa-envelope"></i>
        </a>
        <a href="excursions.php" class="primary-btn cta-tours-btn">
          View Tours <i class="fa fa-eye"></i>
        </a>
      </div>
      <div class="cta-contact-info">
        <p><i class="fa fa-phone"></i> +212 655 23 71 96</p>
        <p><i class="fa fa-envelope"></i> info@travolmorocco.com</p>
      </div>
    </div>
  </section>
  <!-- =========== Call to Action ==================Close-->

  <!-- Back to Top Button -->
  <button class="back-to-top" id="backToTop">
    <i class="fas fa-chevron-up"></i>
  </button>

  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="../assets/js/swiper.js"></script>
  <script src="../assets/js/script.js"></script>

  <script>
    // Simple booking functions
    function bookFlight(destination) {
      alert('Flight booking to ' + destination + ' - Please contact us on WhatsApp at +212 655 23 71 96');
    }

    function bookHotel(destination) {
      alert('Hotel booking in ' + destination + ' - Please contact us on WhatsApp at +212 655 23 71 96');
    }

    function bookTour(tour) {
      alert(tour + ' tour booking - Please visit our Tours page or WhatsApp +212 655 23 71 96');
    }
  </script>
</body>

<footer>
  <?php include '../includes/footer.php'; ?>
</footer>

</html>
<?php ob_end_flush(); ?>