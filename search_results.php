<?php
include('db_connect.php');

header('Content-Type: application/json'); // Ensure JSON output

if (!isset($_GET['query']) || !isset($_GET['type'])) {
    echo json_encode(["error" => "Missing query parameters"]);
    exit();
}

$search = "%" . $_GET['query'] . "%";
$type = $_GET['type'];
$results = [];

if ($type === "doctor") {
    // Fetch doctors with clinic_location
    $query = "SELECT d.id, u.full_name, 
                     COALESCE(d.specialization, 'Unknown') AS specialization, 
                     COALESCE(d.clinic_location, 'Unknown') AS clinic_location
              FROM Doctors d
              JOIN Users u ON d.user_id = u.id
              WHERE d.specialization LIKE ? OR d.clinic_location LIKE ?";

} elseif ($type === "hospital") {
    // Fetch hospitals
    $query = "SELECT id, name, location, specialties AS specialization 
              FROM Hospitals 
              WHERE name LIKE ? OR location LIKE ? OR specialties LIKE ?";
} else {
    echo json_encode(["error" => "Invalid search type"]);
    exit();
}

// Prepare & execute query
$stmt = $conn->prepare($query);
if ($type === "doctor") {
    $stmt->bind_param("ss", $search, $search);
} else {
    $stmt->bind_param("sss", $search, $search, $search);
}
$stmt->execute();
$result = $stmt->get_result();

// Fetch results into an array
while ($row = $result->fetch_assoc()) {
    $results[] = $row;
}

// Debugging: Check if `clinic_location` is being sent
error_log("🔍 Doctor Search Results: " . json_encode($results));

// Return JSON response
echo json_encode($results);
exit();
?>
