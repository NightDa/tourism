Travol Morocco - Tour Booking System
📋 Project Overview
A complete tour booking system for Travol Morocco, featuring customer-facing tour listings and an admin dashboard for managing excursions and bookings.

🏗️ System Structure
text
travol-morocco/
├── index.php                 # Homepage
├── excursions.php            # Tours listing with booking modal
├── process-booking.php       # Booking processor
├── about.php                 # About page
├── contact.php               # Contact page
├── destinations.php          # Destinations page
├── gallery.php               # Gallery page
│
├── admin/                    # Admin panel
│   ├── index.php             # Dashboard + excursion management
│   ├── bookings.php          # Complete booking management
│   ├── login.php             # Admin login with rate limiting
│   ├── logout.php            # Logout handler
│   └── config.php            # Admin configuration
│
├── data/                      # JSON data storage
│   ├── excursions.json        # Tour data
│   ├── hotels.json            # Hotels with pickup zones
│   ├── bookings.json          # All customer bookings
│   └── analytics.json         # Page view tracking
│
├── includes/                  # PHP includes
│   ├── analytics.php          # Page view tracker
│   ├── header.php             # Reusable header
│   └── footer.php             # Reusable footer
│
├── css/                       # Stylesheets
│   ├── style.css
│   ├── responsive.css
│   └── excursions.css
│
├── js/                        # JavaScript files
│   ├── script.js
│   ├── swiper.js
│   └── excursions.js
│
└── img/                       # Images
✨ Key Features
Customer Website
Tour Listings: Browse all excursions with filtering by category

Search & Filter: Real-time search and category filtering

Smart Booking Modal:

Select date

Choose group/private tour

Hotel search with zone-based pickup times

Shopping cart for multiple tours

Price calculation in real-time

Pickup Zones: Automatic pickup time based on hotel zone (A/B/C)

Admin Dashboard
Secure Login: Rate-limited login (5 attempts max, 15-min lockout)

Excursion Management: Add, edit, delete tours

Booking Management:

View all bookings with complete details

Update status (pending → confirmed → completed → cancelled)

Assign bookings to guides (Khalid/Rabii)

WhatsApp and email quick links

Filter by status, assignee, or search

Analytics: Track page views and visits

Recent Bookings: Quick overview on dashboard

🚀 Installation
Requirements
PHP 7.4 or higher

Web server (Apache/Nginx) or PHP built-in server

Write permissions for /data folder

Setup
Clone or upload files to your web server

Set permissions:

bash
chmod 755 data/
chmod 644 data/*.json
Configure (optional):

Update email settings in process-booking.php

Modify pickup times in data/hotels.json

Add/remove tours in admin panel

Running Locally
bash
cd /path/to/travol-morocco
php -S localhost:8000
Access: http://localhost:8000

🔐 Admin Access
Login Page: http://i didnt yet pick the name XD.com/admin/login.php

Default credentials are set in admin/login.php (change in production)

📊 Data Structure
excursions.json
json
{
  "excursions": [
    {
      "id": 1,
      "title": "Medina Tour",
      "category": "cultural",
      "image": "url",
      "duration": "Full Day",
      "location": "Marrakech",
      "description": "...",
      "priceTag": "From 350 MAD",
      "groupPrice": {"adult": 350, "child": 175},
      "privatePrice": {"adult": 650, "child": 325}
    }
  ]
}
hotels.json
Organized by zones (A, B, C) with pickup times for each activity:

agafay: RAK-AGAFAY tours

souk_medina: SOUK ET MEDINA tours

diner_nejjarine: DINER NEJJARINE

cooking_class: COURS DE CUISINE

essaouira: ESSAOUIRA tours

diner_nouba: DINER NOUBA COMPTOIR

ourika: OURIKA tours

📝 Daily Operations
Adding a New Tour
Login to admin dashboard

Fill the "Add New Excursion" form

Click "Save Excursion"

Processing a Booking
Check new bookings in admin dashboard

Click "View" to see complete details

Update status to "confirmed"

Assign to guide (Khalid/Rabii)

Contact customer via WhatsApp/email

After tour, mark as "completed"

Managing Pickup Times
Zone A: Base time (e.g., Agafay 13H00)

Zone B: +15 minutes (Agafay 13H15)

Zone C: +30 minutes (Agafay 13H30)

Hotels outside Medina: "Reception" pickup

⚙️ Configuration Options
Email Settings
In process-booking.php, update:

php
$to = "your-email@example.com"; // Change to your email
Pickup Times
Edit data/hotels.json to modify:

Zone pickup times

Add new hotels

Change meeting points

Tours
Add/modify via admin panel or edit data/excursions.json

🔒 Security Notes
Admin login has rate limiting (5 attempts)

Sessions expire after browser close

JSON files are not web-accessible

Passwords stored in PHP files (not public)

🐛 Troubleshooting
Bookings not saving?
Check /data folder write permissions

Verify bookings.json exists and is writable

Hotel search not working?
Ensure hotels.json is valid JSON

Check browser console for errors

Styles not loading?
Clear browser cache

Verify CSS file paths

📱 Mobile Responsive
The system works on:

Desktop computers

Tablets

Mobile phones

🤝 Support
For issues or questions:

Check browser console for errors

Verify PHP error logs

Ensure all file permissions are correct

Built with: PHP, jQuery, HTML5, CSS3
Data storage: JSON files (no database needed)
Last updated: February 2025