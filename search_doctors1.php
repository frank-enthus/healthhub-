<?php
session_start();
require 'db_connect.php';

// Check if the user is logged in and is a patient
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "patient") {
    echo json_encode([]);
    exit;
}

// Get the search term from the query string
$search = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : "%";

// Query to search for doctors by name
$query = "SELECT id, full_name FROM users WHERE user_type = 'doctor' AND full_name LIKE ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    // Handle SQL error
    echo json_encode(['error' => 'SQL prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

$doctors = [];
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}

echo json_encode($doctors);
?>