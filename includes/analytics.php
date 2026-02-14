<?php
// Simple analytics tracker
function trackPageView()
{
    $analytics_file = __DIR__ . '/../data/analytics.json';

    // Get current page
    $current_page = basename($_SERVER['PHP_SELF']);

    // Read existing data
    if (file_exists($analytics_file)) {
        $analytics = json_decode(file_get_contents($analytics_file), true);
    } else {
        // Initialize if file doesn't exist
        $analytics = [
            'page_views' => [],
            'total_visits' => 0,
            'last_reset' => date('Y-m-d')
        ];
    }

    // Increment page view
    if (!isset($analytics['page_views'][$current_page])) {
        $analytics['page_views'][$current_page] = 0;
    }
    $analytics['page_views'][$current_page]++;
    $analytics['total_visits']++;

    // Save back to file
    file_put_contents($analytics_file, json_encode($analytics, JSON_PRETTY_PRINT));
}

// Call the function
trackPageView();
