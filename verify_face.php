<?php
ob_start(); // Start output buffering
error_reporting(0); // Suppress warnings/notices

header('Content-Type: application/json');

// Include database connection
require 'database.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if required parameters are set
if (!isset($_POST['user_id']) || !isset($_POST['image_data'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

// Get parameters
$user_id = $_POST['user_id'];
$image_data = $_POST['image_data'];

// Validate and sanitize user_id
if (!preg_match('/^[a-zA-Z0-9]+$/', $user_id)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid user ID format']);
    exit;
}

// Validate image data (should start with data:image/jpeg;base64,)
if (strpos($image_data, 'data:image/jpeg;base64,') !== 0) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid image format']);
    exit;
}

// Decode base64 image
$base64_image = substr($image_data, strpos($image_data, ',') + 1);
$decoded_image = base64_decode($base64_image);
if ($decoded_image === false) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Failed to decode image']);
    exit;
}

// Ensure user_images directory exists
$user_images_dir = 'user_images';
if (!file_exists($user_images_dir)) {
    mkdir($user_images_dir, 0755, true);
}

// Temporary path
$temp_image_path = "$user_images_dir/temp_{$user_id}.jpg";
file_put_contents($temp_image_path, $decoded_image);

// Reference image path
$reference_image_path = "$user_images_dir/{$user_id}.jpg";

// If first-time, save reference and respond
if (!file_exists($reference_image_path)) {
    copy($temp_image_path, $reference_image_path);
    unlink($temp_image_path);
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'First-time user. Image stored as reference for future verification.',
        'is_new_user' => true
    ]);
    exit;
}

// Compare using Face++ API
$verification_result = compareWithFacePlusPlus($temp_image_path, $reference_image_path, $user_id);

// Clean up temporary image
unlink($temp_image_path);

// Output the JSON response
ob_end_clean();
echo json_encode($verification_result);
exit;

/**
 * Compare two face images using Face++ API
 */
function compareWithFacePlusPlus($img1, $img2, $user_id) {
    $api_key = 'xu6EL5uXrbgpOqvZoRiJJlMXF0lKtWiB';
    $api_secret = '8zLoaVFzOdfn3EIrHv4KsQQzXVAbDPJt';
    $url = 'https://api-us.faceplusplus.com/facepp/v3/compare';

    $post_fields = [
        'api_key'    => $api_key,
        'api_secret' => $api_secret,
        'image_file1'=> new CURLFile($img1),
        'image_file2'=> new CURLFile($img2)
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

    $response = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);
    
    if ($http_status === 200 && isset($result['confidence'])) {
        $success = ($result['confidence'] >= 80);
        logVerificationAttempt($user_id, $success);
        return [
            'success'    => $success,
            'message'    => $success ? 'Facial verification successful.' : 'Facial verification failed. Face does not match.',
            'confidence' => $result['confidence']
        ];
    }

    // API error
    return [
        'success' => false,
        'message' => 'Face++ API error',
        'error'   => $result
    ];
}

/**
 * Log verification attempts for audit
 */
function logVerificationAttempt($user_id, $success) {
    $log_dir  = 'logs';
    $log_file = "$log_dir/verification_log.txt";
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    $entry = sprintf("%s - IP: %s - User: %s - %s%s", 
        date('Y-m-d H:i:s'),
        $_SERVER['REMOTE_ADDR'], 
        $user_id,
        $success ? 'SUCCESS' : 'FAILED',
        PHP_EOL
    );
    file_put_contents($log_file, $entry, FILE_APPEND);
}
