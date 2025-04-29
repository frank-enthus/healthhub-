<?php
require_once "db_connect.php";

if (!isset($_GET["query"]) || trim($_GET["query"]) === "") {
    echo json_encode([]); // Return empty response if no query
    exit();
}

$query = "%" . $_GET["query"] . "%";

$sql = "SELECT users.id AS user_id, users.full_name, doctors.id AS doctor_id, doctors.specialization 
        FROM users
        INNER JOIN doctors ON users.id = doctors.user_id
        WHERE users.full_name LIKE ? OR doctors.specialization LIKE ?
        LIMIT 10";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "Database query error: " . $conn->error]);
    exit();
}

$stmt->bind_param("ss", $query, $query);
$stmt->execute();
$result = $stmt->get_result();

$doctors = [];
while ($row = $result->fetch_assoc()) {
    $doctors[] = [
        "doctor_id" => $row['doctor_id'], // Include doctor ID
        "full_name" => $row['full_name'],
        "specialization" => $row['specialization']
    ];
}

// Output JSON response
header('Content-Type: application/json');
echo json_encode($doctors);
?>
