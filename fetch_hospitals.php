<?php
include('db_connect.php');



$query = "SELECT name, location, image, website, latitude, longitude FROM Hospitals";
$result = mysqli_query($conn, $query);

$hospitals = [];

while ($row = mysqli_fetch_assoc($result)) {
    $hospitals[] = $row;
}

header('Content-Type: application/json');
echo json_encode($hospitals);
?>
