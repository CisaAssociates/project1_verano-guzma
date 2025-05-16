<?php
// Connect to database
require 'database.php';
$conn = Database::connect();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get values from form
$id = $_POST['id'];
$name = $_POST['name'];
$gender = $_POST['gender'];
$email = $_POST['email'];
$mobile = $_POST['mobile'];

// Handle image upload
$user_image = '';
$image_path = '';

// Check if image data was submitted
if (isset($_POST['user_image']) && !empty($_POST['user_image'])) {
    // Create directory if it doesn't exist
    $target_dir = "user_images/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Always use user_id.jpg as filename for reference image
    $image_filename = $id . '.jpg';
    $image_path = $target_dir . $image_filename;
    
    // Get image data from base64 encoded string
    $image_parts = explode(";base64,", $_POST['user_image']);
    if (count($image_parts) > 1) {
        $image_data = base64_decode($image_parts[1]);
        
        // Save image to file (overwrite if exists)
        if (file_put_contents($image_path, $image_data)) {
            $user_image = $image_path;
        }
    }
}

// Insert user data into database
$sql = "INSERT INTO table_the_iot_projects (id, name, gender, email, mobile, image_path) 
        VALUES (?, ?, ?, ?, ?, ?)";

try {
    // Prepare and execute SQL statement
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([$id, $name, $gender, $email, $mobile, $user_image]);
    
    if ($result) {
        // Registration successful
        echo '<script>
            alert("New record created successfully!");
            window.location.href = "user data.php";
        </script>';
    } else {
        // Registration failed
        echo '<script>
            alert("Error: Failed to insert record");
            window.location.href = "registration.php";
        </script>';
    }
} catch (PDOException $e) {
    // Database error
    echo '<script>
        alert("Database Error: ' . $e->getMessage() . '");
        window.location.href = "registration.php";
    </script>';
}

// Close connection
Database::disconnect();
?>