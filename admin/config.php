<?php
session_start();

// Check if user is logged in
function isLoggedIn()
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Redirect if not logged in
function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Data file path
define('DATA_FILE', __DIR__ . '/../data/excursions.json');

// Ensure data directory exists
if (!file_exists(__DIR__ . '/../data')) {
    mkdir(__DIR__ . '/../data', 0777, true);
}

// Initialize data file if it doesn't exist
if (!file_exists(DATA_FILE)) {
    $initialData = [
        'excursions' => [],
        'last_updated' => date('Y-m-d H:i:s')
    ];
    file_put_contents(DATA_FILE, json_encode($initialData, JSON_PRETTY_PRINT));
}

// Load data
function loadData()
{
    return json_decode(file_get_contents(DATA_FILE), true);
}

// Save data
function saveData($data)
{
    $data['last_updated'] = date('Y-m-d H:i:s');
    return file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT));
}

// Get next ID
function getNextId($data)
{
    $maxId = 0;
    foreach ($data['excursions'] as $excursion) {
        if ($excursion['id'] > $maxId) {
            $maxId = $excursion['id'];
        }
    }
    return $maxId + 1;
}

// Add CSRF token
function generateCSRF()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRF($token)
{
    return isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === $token;
}
