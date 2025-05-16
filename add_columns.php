<?php
// Include database connection
require 'database.php';
$conn = Database::connect();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // Add missing columns
    $conn->exec("ALTER TABLE table_the_iot_projects 
                 ADD COLUMN IF NOT EXISTS mobile VARCHAR(20),
                 ADD COLUMN IF NOT EXISTS image_path VARCHAR(255)");
    
    echo "Columns added successfully!";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

Database::disconnect();
?> 