<?php
ob_start();
require_once '../includes/analytics.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Travol Morocco</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        .contact-hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/img/contact-hero.jpg');
            background-size: cover;
            background-position: center;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin: 50px 0;
        }

        .contact-info {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .info-item i {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 6px;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert.success {
            background: #d4edda;
            color: #155724;
        }

        .alert.error {
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <section class="contact-hero">
        <div class="container">
            <h1>Get In Touch</h1>
            <p>We'd love to hear from you</p>
        </div>
    </section>

    <div class="container">
        <div class="contact-grid">
            <div class="contact-info">
                <h2 style="margin-bottom: 30px;">Contact Information</h2>

                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h4>Visit Us</h4>
                        <p>Marrakech, Morocco<br>Medina - Bab Doukkala</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h4>Call Us</h4>
                        <p>+212 524 43 34 51<br>+212 655 23 71 96 (WhatsApp)</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h4>Email Us</h4>
                        <p>info@travolmorocco.com<br>reservationrak@sti.ma</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h4>Working Hours</h4>
                        <p>Monday - Saturday: 9:00 - 20:00<br>Sunday: 10:00 - 18:00</p>
                    </div>
                </div>

                <div style="margin-top: 30px;">
                    <h4>Follow Us</h4>
                    <div style="display: flex; gap: 15px; margin-top: 15px;">
                        <a href="#" style="width: 40px; height: 40px; background: #667eea; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" style="width: 40px; height: 40px; background: #667eea; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i class="fab fa-instagram"></i></a>
                        <a href="#" style="width: 40px; height: 40px; background: #667eea; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>

            <div>
                <h2 style="margin-bottom: 30px;">Send Us a Message</h2>

                <?php
                if (isset($_GET['success'])) {
                    echo '<div class="alert success"><i class="fas fa-check-circle"></i> Message sent successfully! We\'ll get back to you soon.</div>';
                }
                if (isset($_GET['error'])) {
                    echo '<div class="alert error"><i class="fas fa-exclamation-circle"></i> Error sending message. Please try again.</div>';
                }
                ?>

                <form action="../contact-handler.php" method="POST" id="contactForm">
                    <div class="form-group">
                        <label>Your Name *</label>
                        <input type="text" name="name" required>
                    </div>

                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" placeholder="+212 XXX XXXXXX">
                    </div>

                    <div class="form-group">
                        <label>Subject *</label>
                        <select name="subject" required>
                            <option value="">Select a subject</option>
                            <option value="booking">Booking Question</option>
                            <option value="tour">Tour Information</option>
                            <option value="custom">Custom Tour Request</option>
                            <option value="feedback">Feedback</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Message *</label>
                        <textarea name="message" rows="6" required></textarea>
                    </div>

                    <!-- Honeypot for spam prevention -->
                    <div style="display: none;">
                        <input type="text" name="website" value="">
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Map -->
    <section style="margin: 50px 0;">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3397.456789012345!2d-7.987654321098765!3d31.629543210987654!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzHCsDM3JzQ2LjMiTiA3wrA1OScxNy4xIlc!5e0!3m2!1sen!2sma!4v1234567890123!5m2!1sen!2sma"
            width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </section>

    <?php include '../includes/footer.php'; ?>
</body>

</html>
<?php ob_end_flush(); ?>