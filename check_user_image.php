<?php
// Set headers to handle the AJAX request
header('Content-Type: application/json');

// Check if user_id parameter is provided
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'User ID not provided']);
    exit;
}

// Get and sanitize user_id
$user_id = $_GET['id'];
if (!preg_match('/^[a-zA-Z0-9]+$/', $user_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID format']);
    exit;
}

// Path where user images are stored
$user_images_dir = 'user_images';
$image_path = $user_images_dir . '/' . $user_id . '.jpg';

// Check if image file exists
if (file_exists($image_path)) {
    echo json_encode([
        'success' => true,
        'has_image' => true,
        'image_path' => $image_path
    ]);
} else {
    echo json_encode([
        'success' => true,
        'has_image' => false,
        'message' => 'No facial image found for this user'
    ]);
}
exit;
?>