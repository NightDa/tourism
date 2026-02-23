<?php
require_once 'includes/logger.php';
require_once 'includes/mail_phpmailer.php'; // This line is correct

// Honeypot check
if (!empty($_POST['website'])) {
    // Bot detected, silently redirect
    header('Location: pages/contact.php?success=1');
    exit;
}

// Validate required fields
$required = ['name', 'email', 'subject', 'message'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        header('Location: pages/contact.php?error=missing_fields');
        exit;
    }
}

// Validate email
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    header('Location: pages/contact.php?error=invalid_email');
    exit;
}

// Prepare contact data
$contact = [
    'id' => 'CONTACT_' . time() . rand(100, 999),
    'name' => $_POST['name'],
    'email' => $_POST['email'],
    'phone' => $_POST['phone'] ?? '',
    'subject' => $_POST['subject'],
    'message' => $_POST['message'],
    'ip' => $_SERVER['REMOTE_ADDR'],
    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
    'created_at' => date('Y-m-d H:i:s'),
    'status' => 'new'
];

// Save to contacts.json
$contacts_file = __DIR__ . '/data/contacts.json';
$contacts = ['contacts' => []];

if (file_exists($contacts_file)) {
    $contacts = json_decode(file_get_contents($contacts_file), true);
}

$contacts['contacts'][] = $contact;
file_put_contents($contacts_file, json_encode($contacts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Send email notification
sendContactNotification($contact);

// Log the contact
logMessage("New contact form submission: {$contact['name']} - {$contact['subject']}", 'info');

// Redirect with success
header('Location: pages/contact.php?success=1');
exit;
