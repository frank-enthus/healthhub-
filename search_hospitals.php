<?php
include('db_connect.php');

if (isset($_GET['query'])) {
    $search = "%" . $_GET['query'] . "%";

    $query = "SELECT id, name, location, specialties AS specialization FROM Hospitals 
              WHERE name LIKE ? OR location LIKE ? OR specialties LIKE ? LIMIT 10";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $search, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $hospitals = [];
    while ($row = $result->fetch_assoc()) {
        $hospitals[] = $row;
    }

    echo json_encode($hospitals);
}
?>
