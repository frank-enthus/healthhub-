<?php
include('db_connect.php');

// Admin credentials
$full_name = "Admin User";
$email = "admin@healthhub.com";
$password = password_hash("admin123", PASSWORD_DEFAULT); // Hash the password

// Insert admin
$query = "INSERT INTO Admins (full_name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $full_name, $email, $password);

if ($stmt->execute()) {
    echo "Admin created successfully!";
} else {
    echo "Error: " . $stmt->error;
}
?>
