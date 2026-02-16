<?php
// Start output buffering
ob_start();

// Admin check for quick link
session_start();
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Include analytics
require_once 'includes/analytics.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Travol Morocco | Your Local Travel Partner</title>
    <meta name="description" content="Meet the team behind Travol Morocco. We're a local startup passionate about sharing the real Morocco with travelers like you.">

    <!-- Open Graph -->
    <meta property="og:title" content="About Travol Morocco - Your Local Travel Partner">
    <meta property="og:description" content="We're a small, passionate team dedicated to creating authentic Moroccan experiences.">
    <meta property="og:image" content="./img/logo.png">

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="css/responsive.css">

    <style>
        .about-hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('./img/moroccan-pattern.jpg');
            background-size: cover;
            background-position: center;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .about-hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .about-hero p {
            font-size: 20px;
            max-width: 600px;
            margin: 0 auto;
            opacity: 0.95;
        }

        .section {
            padding: 80px 0;
        }

        .section-light {
            background: white;
        }

        .section-gray {
            background: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 36px;
            color: #333;
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }

        .section-title h2:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .section-title p {
            color: #666;
            font-size: 18px;
            max-width: 700px;
            margin: 20px auto 0;
        }

        /* Story Section */
        .story-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }

        .story-content h3 {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
        }

        .story-content p {
            color: #666;
            line-height: 1.8;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .story-content .highlight {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
        }

        .story-content .highlight i {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .story-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .stat-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s;
        }

        .stat-box:hover {
            transform: translateY(-5px);
        }

        .stat-box i {
            font-size: 40px;
            color: #667eea;
            margin-bottom: 15px;
        }

        .stat-box .number {
            font-size: 36px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-box .label {
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Team Section */
        .team-intro {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 50px;
            color: #666;
            font-size: 18px;
            line-height: 1.6;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 30px;
        }

        .team-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s;
        }

        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.2);
        }

        .team-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .team-icon i {
            font-size: 40px;
            color: white;
        }

        .team-card h3 {
            font-size: 22px;
            color: #333;
            margin-bottom: 5px;
        }

        .team-card .role {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .team-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .team-contact {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            padding: 8px 20px;
            border: 2px solid #667eea;
            border-radius: 25px;
            transition: all 0.3s;
        }

        .team-contact:hover {
            background: #667eea;
            color: white;
        }

        /* How We Work */
        .process-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-top: 40px;
        }

        .process-step {
            text-align: center;
            position: relative;
        }

        .process-step:not(:last-child):after {
            content: '→';
            position: absolute;
            right: -15px;
            top: 30px;
            font-size: 24px;
            color: #667eea;
            font-weight: bold;
        }

        .step-number {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            margin: 0 auto 20px;
        }

        .process-step h4 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }

        .process-step p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }

        /* Promise Section */
        .promise-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 30px;
        }

        .promise-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s;
        }

        .promise-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
        }

        .promise-card i {
            font-size: 40px;
            color: #667eea;
            margin-bottom: 20px;
        }

        .promise-card h3 {
            font-size: 20px;
            color: #333;
            margin-bottom: 15px;
        }

        .promise-card p {
            color: #666;
            line-height: 1.6;
            font-size: 14px;
        }

        /* Contact CTA */
        .contact-cta {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 60px;
            border-radius: 10px;
        }

        .contact-cta h2 {
            font-size: 32px;
            margin-bottom: 20px;
        }

        .contact-cta p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .contact-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .contact-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .contact-btn.whatsapp {
            background: #25D366;
            color: white;
        }

        .contact-btn.email {
            background: white;
            color: #333;
        }

        .contact-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 992px) {
            .story-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .team-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .process-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .process-step:not(:last-child):after {
                display: none;
            }

            .promise-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .about-hero h1 {
                font-size: 32px;
            }

            .about-hero p {
                font-size: 16px;
            }

            .section-title h2 {
                font-size: 28px;
            }

            .team-grid {
                grid-template-columns: 1fr;
            }

            .process-grid {
                grid-template-columns: 1fr;
            }

            .promise-grid {
                grid-template-columns: 1fr;
            }

            .contact-buttons {
                flex-direction: column;
            }

            .contact-cta {
                padding: 40px 20px;
            }
        }
    </style>
</head>

<body>
    <?php if ($is_admin): ?>
        <a href="admin/index.php" class="admin-quick-link" style="bottom: 140px;">
            <i class="fas fa-cog"></i> Admin Dashboard
        </a>
    <?php endif; ?>

    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <h1>Your Local Travel Partner in Morocco</h1>
            <p>A small team with big passion for sharing the real Morocco</p>
        </div>
    </section>

    <!-- Our Story -->
    <section class="section section-light">
        <div class="container">
            <div class="story-grid">
                <div class="story-content">
                    <h3>How It All Started</h3>
                    <p>Travol Morocco began as a simple idea: show travelers the real Morocco, not just the tourist brochures. What started as helping a few friends discover Marrakech's hidden gems grew into something bigger.</p>
                    <p>Today, I personally handle every booking and message. When you contact us, you're talking directly to the person who built this business from the ground up. No call centers, no automated responses - just real conversations with someone who genuinely cares about your experience.</p>
                    <p>I've partnered with two trusted colleagues who handle the on-the-ground coordination - meeting clients, collecting payments, and ensuring everything runs smoothly. Together with our network of reliable drivers, we're building something authentic.</p>
                    <div class="highlight">
                        <i class="fas fa-heart"></i>
                        <h4>My Personal Guarantee</h4>
                        <p>Every booking matters to me. If anything isn't right, I personally make it right. Your Moroccan adventure is my reputation.</p>
                    </div>
                </div>
                <div class="story-stats">
                    <div class="stat-box">
                        <i class="fas fa-smile"></i>
                        <div class="number">150+</div>
                        <div class="label">Happy Travelers</div>
                    </div>
                    <div class="stat-box">
                        <i class="fas fa-map-marked-alt"></i>
                        <div class="number">8</div>
                        <div class="label">Destinations</div>
                    </div>
                    <div class="stat-box">
                        <i class="fas fa-clock"></i>
                        <div class="number">24/7</div>
                        <div class="label">Personal Support</div>
                    </div>
                    <div class="stat-box">
                        <i class="fas fa-star"></i>
                        <div class="number">100%</div>
                        <div class="label">Personal Commitment</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- The Team -->
    <section class="section section-gray">
        <div class="container">
            <div class="section-title">
                <h2>The People Behind Your Journey</h2>
                <p>We're a small team, but we handle everything personally - from your first message to your final goodbye.</p>
            </div>

            <div class="team-grid">
                <!-- Founder/Manager -->
                <div class="team-card">
                    <div class="team-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3>Mhamed</h3>
                    <div class="role">Founder & Your Contact</div>
                    <p>I'm the one answering your messages, building the website, and personally ensuring every booking is perfect. When you message us on WhatsApp, you're talking to me.</p>
                    <a href="https://wa.me/212655237196" class="team-contact" target="_blank">
                        <i class="fab fa-whatsapp"></i> Message Me
                    </a>
                </div>

                <!-- Khalid -->
                <div class="team-card">
                    <div class="team-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Khalid</h3>
                    <div class="role">On-Site Coordinator</div>
                    <p>Khalid is the friendly face who meets you at the pickup point, handles payments, and makes sure you start your tour with a smile. He's your first point of contact in Marrakech.</p>
                </div>

                <!-- Rabii -->
                <div class="team-card">
                    <div class="team-icon">
                        <i class="fas fa-map-signs"></i>
                    </div>
                    <h3>Rabii</h3>
                    <div class="role">Tour Coordinator</div>
                    <p>Rabii manages our network of trusted drivers and ensures every pickup happens on time. If there's ever an issue, he's the one solving it on the ground.</p>
                </div>
            </div>

            <div style="text-align: center; margin-top: 40px; color: #666;">
                <i class="fas fa-truck"></i> Plus our trusted network of professional drivers who know the roads to Agafay, Ourika, and beyond.
            </div>
        </div>
    </section>

    <!-- How We Work -->
    <section class="section section-light">
        <div class="container">
            <div class="section-title">
                <h2>How It Works</h2>
                <p>Simple, personal, and reliable</p>
            </div>

            <div class="process-grid">
                <div class="process-step">
                    <div class="step-number">1</div>
                    <h4>You Book Online</h4>
                    <p>Choose your tour, select your hotel, and book through our simple system. I receive your booking instantly.</p>
                </div>
                <div class="process-step">
                    <div class="step-number">2</div>
                    <h4>I Confirm & Assign</h4>
                    <p>I personally confirm your booking and assign Khalid or Rabii as your on-site contact based on your pickup zone.</p>
                </div>
                <div class="process-step">
                    <div class="step-number">3</div>
                    <h4>Day of Your Tour</h4>
                    <p>Your assigned coordinator meets you at the pickup point at the scheduled time. You meet your driver and enjoy your tour.</p>
                </div>
                <div class="process-step">
                    <div class="step-number">4</div>
                    <h4>Pay & Enjoy</h4>
                    <p>Pay in cash to your coordinator. No advance payment, no stress - just enjoy your Moroccan adventure.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Promise -->
    <section class="section section-gray">
        <div class="container">
            <div class="section-title">
                <h2>Our Promise to You</h2>
                <p>We might be small, but we're 100% committed to your experience</p>
            </div>

            <div class="promise-grid">
                <div class="promise-card">
                    <i class="fas fa-clock"></i>
                    <h3>Always On Time</h3>
                    <p>Our zone-based pickup system means we know exactly when to meet you. No waiting, no stress.</p>
                </div>
                <div class="promise-card">
                    <i class="fas fa-comments"></i>
                    <h3>Real Human Contact</h3>
                    <p>When you message, you get me. When you arrive, you meet Khalid or Rabii. Real people, not robots.</p>
                </div>
                <div class="promise-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Pay On Arrival</h3>
                    <p>No advance payment needed. You pay in cash when we meet. If plans change, just let me know.</p>
                </div>
                <div class="promise-card">
                    <i class="fas fa-hand-holding-heart"></i>
                    <h3>Personal Guarantee</h3>
                    <p>This is my business, my reputation. If anything isn't right, I personally make it right. Period.</p>
                </div>
                <div class="promise-card">
                    <i class="fas fa-phone-alt"></i>
                    <h3>24/7 Support</h3>
                    <p>My WhatsApp is always on. Questions before booking? Issues during your tour? I'm just a message away.</p>
                </div>
                <div class="promise-card">
                    <i class="fas fa-handshake"></i>
                    <h3>Local Knowledge</h3>
                    <p>We're not a big corporation. We're locals who know every corner of Marrakech and beyond.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="section section-light">
        <div class="container">
            <div class="contact-cta">
                <h2>Have Questions? Want to Customize a Tour?</h2>
                <p>Message me directly on WhatsApp. I usually respond within minutes.</p>
                <div class="contact-buttons">
                    <a href="https://wa.me/212655237196" class="contact-btn whatsapp" target="_blank">
                        <i class="fab fa-whatsapp"></i> Message on WhatsApp
                    </a>
                    <a href="mailto:reservationrak@sti.ma" class="contact-btn email">
                        <i class="fas fa-envelope"></i> Send Email
                    </a>
                </div>
                <p style="margin-top: 20px; font-size: 14px; opacity: 0.8;">
                    <i class="fas fa-phone"></i> Or call: +212 655 23 71 96
                </p>
            </div>
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