<?php
// record_access.php

// 1) Pull in your PDO wrapper (if you still use it elsewhere)…
require 'database.php';

// 2) …then create a mysqli connection for this script
$conn = new mysqli('localhost','root','','nodemcu_rfid_iot_projects');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 3) Grab & validate inputs
$user_id = isset($_GET['id'])   ? $_GET['id']   : '';
$mode    = isset($_GET['mode']) ? $_GET['mode'] : '';
if (!$user_id || !$mode) {
    echo "Error: Invalid parameters";
    exit;
}

// 4) Timestamp for any UPDATEs
$timestamp = date('Y-m-d H:i:s');

if ($mode === 'attendance') {
    // — Simply log a PRESENT event —
    $sql = "INSERT INTO attendance_logs (user_id, status) VALUES (?, 'Present')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    echo "Attendance recorded.";
    $stmt->close();

} else if ($mode === 'gatepass') {
    // — Check the very last gatepass row for this user —
    $check = $conn->prepare("
        SELECT entry_time, exit_time 
          FROM gatepass_logs 
         WHERE user_id = ? 
      ORDER BY entry_time DESC 
         LIMIT 1
    ");
    $check->bind_param("s", $user_id);
    $check->execute();
    $res = $check->get_result();

    if ($row = $res->fetch_assoc()) {
        if (is_null($row['exit_time'])) {
            // The last record has no exit_time → mark it as "exit"
            $upd = $conn->prepare("
                UPDATE gatepass_logs
                   SET exit_time = ?
                 WHERE user_id = ? 
                   AND exit_time IS NULL
              ORDER BY entry_time DESC
                 LIMIT 1
            ");
            $upd->bind_param("ss", $timestamp, $user_id);
            $upd->execute();
            echo "Gate exit recorded.";
            $upd->close();
        } else {
            // Last record already had an exit_time → insert a new "entry"
            $ins = $conn->prepare("
                INSERT INTO gatepass_logs (user_id)
                VALUES (?)
            ");
            $ins->bind_param("s", $user_id);
            $ins->execute();
            echo "Gate entry recorded.";
            $ins->close();
        }

    } else {
        // No prior record → first time through, treat as "entry"
        $ins = $conn->prepare("
            INSERT INTO gatepass_logs (user_id)
            VALUES (?)
        ");
        $ins->bind_param("s", $user_id);
        $ins->execute();
        echo "Gate entry recorded.";
        $ins->close();
    }

    $check->close();
}

$conn->close();
?>
