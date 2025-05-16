<?php
// Include database connection
require 'database.php';
$conn = Database::connect();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // Step 1: Check if columns exist
    $result = $conn->query("SHOW COLUMNS FROM table_the_iot_projects LIKE 'image_path'");
    $image_path_exists = $result->rowCount() > 0;
    
    $result = $conn->query("SHOW COLUMNS FROM table_the_iot_projects LIKE 'mobile'");
    $mobile_exists = $result->rowCount() > 0;
    
    // Step 2: Add missing columns if needed
    if (!$mobile_exists) {
        $conn->exec("ALTER TABLE table_the_iot_projects ADD COLUMN mobile VARCHAR(100)");
        echo "Added 'mobile' column<br>";
    }
    
    if (!$image_path_exists) {
        $conn->exec("ALTER TABLE table_the_iot_projects ADD COLUMN image_path VARCHAR(255)");
        echo "Added 'image_path' column<br>";
    }
    
    // Step 3: Show all columns
    $result = $conn->query("SHOW COLUMNS FROM table_the_iot_projects");
    echo "<h2>Current Table Structure:</h2>";
    
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "Field: " . $row['Field'] . ", Type: " . $row['Type'] . "<br>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

Database::disconnect();
?> 