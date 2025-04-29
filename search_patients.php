<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "doctor") {
    echo json_encode([]);
    exit;
}

$search = isset($_GET['query']) ? "%" . $_GET['query'] . "%" : "%";

// Query to search for patients by name
$query = "SELECT id, full_name FROM users WHERE user_type = 'patient' AND full_name LIKE ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

$patients = [];
while ($row = $result->fetch_assoc()) {
    $patients[] = $row;
}

echo json_encode($patients);
?>