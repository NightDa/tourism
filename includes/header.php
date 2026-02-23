<header>
    <div class="container">
        <nav>
            <div class="logo">
                <img src="../assets/img/Sans titre.png" alt="Travol Morocco" />
            </div>
            <ul>
                <div class="btn">
                    <i class="fas fa-times close-btn"></i>
                </div>
                <li><a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>Home</a></li>
                <li><a href="about.php" <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'class="active"' : ''; ?>>About</a></li>
                <li><a href="excursions.php" <?php echo basename($_SERVER['PHP_SELF']) == 'excursions.php' ? 'class="active"' : ''; ?>>Tours</a></li>
                <li><a href="destinations.php" <?php echo basename($_SERVER['PHP_SELF']) == 'destinations.php' ? 'class="active"' : ''; ?>>Destinations</a></li>
                <li><a href="packages.php" <?php echo basename($_SERVER['PHP_SELF']) == 'packages.php' ? 'class="active"' : ''; ?>>Packages</a></li>
                <li><a href="contact.php" <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'class="active"' : ''; ?>>Contact</a></li>
            </ul>
            <div class="btn">
                <i class="fas fa-bars menu-btn"></i>
            </div>
        </nav>
    </div>
</header>