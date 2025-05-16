<?php
require 'database.php';
$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$mode = isset($_POST['mode']) ? $_POST['mode'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : null; // For attendance

if ($user_id && $mode) {
    $currentTimestamp = date('Y-m-d H:i:s'); // Get the current timestamp

    if ($mode === 'attendance') {
        // Insert attendance log with timestamp
        $sql = "INSERT INTO attendance_logs (user_id, status, timestamp) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $status ? $status : 'Present', $currentTimestamp]);
        echo json_encode(['success' => true, 'type' => 'attendance']);
    } elseif ($mode === 'gatepass') {
        // Insert gatepass entry log with entry_time
        $sql = "INSERT INTO gatepass_logs (user_id, entry_time) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $currentTimestamp]);
        echo json_encode(['success' => true, 'type' => 'gatepass']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid mode']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
}
Database::disconnect();
