<?php
include('db_connect.php'); // Ensure this file correctly connects to your database

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';

$query = "SELECT id, name, location FROM hospitals WHERE 1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND name LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

if (!empty($location)) {
    $query .= " AND location = ?";
    $params[] = $location;
    $types .= "s";
}

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$hospitals = [];
while ($row = $result->fetch_assoc()) {
    $hospitals[] = $row;
}

echo json_encode($hospitals);
?>
