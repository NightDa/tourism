<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Travol Morocco</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        .error-page {
            min-height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 50px 20px;
        }

        .error-code {
            font-size: 120px;
            font-weight: 800;
            color: #667eea;
            line-height: 1;
            margin-bottom: 20px;
            text-shadow: 3px 3px 0 rgba(102, 126, 234, 0.2);
        }

        .error-title {
            font-size: 32px;
            color: #333;
            margin-bottom: 20px;
        }

        .error-text {
            color: #666;
            max-width: 500px;
            margin: 0 auto 30px;
        }

        .search-box {
            max-width: 400px;
            margin: 30px auto;
        }

        .search-box input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e1e1;
            border-radius: 50px;
        }

        .popular-links {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .popular-links a {
            color: #667eea;
            text-decoration: none;
            padding: 8px 20px;
            border: 2px solid #667eea;
            border-radius: 50px;
            transition: all 0.3s;
        }

        .popular-links a:hover {
            background: #667eea;
            color: white;
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="error-page">
        <div class="container">
            <div class="error-code">404</div>
            <h1 class="error-title">Oops! Page Not Found</h1>
            <p class="error-text">The page you're looking for doesn't exist or has been moved.</p>

            <a href="pages/index.php" class="primary-btn" style="padding: 12px 30px;">
                <i class="fas fa-home"></i> Back to Home
            </a>

            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search for tours or destinations...">
            </div>

            <div class="popular-links">
                <a href="pages/excursions.php"><i class="fas fa-hiking"></i> Tours</a>
                <a href="pages/destinations.php"><i class="fas fa-map-marked-alt"></i> Destinations</a>
                <a href="pages/packages.php"><i class="fas fa-box"></i> Packages</a>
                <a href="pages/contact.php"><i class="fas fa-envelope"></i> Contact</a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                window.location.href = 'pages/excursions.php?search=' + encodeURIComponent(this.value);
            }
        });
    </script>

    <?php include 'includes/footer.php'; ?>
</body>

</html>