<?php
require_once 'config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image_file'])) {
    $target_dir = "uploads/";

    // Create uploads directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = time() . '_' . basename($_FILES["image_file"]["name"]);
    $target_file = $target_dir . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is actual image
    $check = getimagesize($_FILES["image_file"]["tmp_name"]);
    if ($check === false) {
        echo json_encode(['success' => false, 'error' => 'File is not an image.']);
        exit;
    }

    // Check file size (5MB max)
    if ($_FILES["image_file"]["size"] > 5000000) {
        echo json_encode(['success' => false, 'error' => 'File is too large. Max 5MB.']);
        exit;
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        echo json_encode(['success' => false, 'error' => 'Only JPG, JPEG, PNG, GIF & WEBP files are allowed.']);
        exit;
    }

    if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_file)) {
        // Return the URL
        echo json_encode([
            'success' => true,
            'url' => 'uploads/' . $file_name
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error uploading file.']);
    }
    exit;
}
