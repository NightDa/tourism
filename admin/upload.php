<?php
require_once 'config.php';
requireLogin();

if ($_FILES['image_file']) {
    $target_dir = "uploads/";
    $file_name = time() . '_' . basename($_FILES["image_file"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_file)) {
        echo json_encode(['success' => true, 'url' => 'uploads/' . $file_name]);
    }
}
