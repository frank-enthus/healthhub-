<?php
// Enable error reporting (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include('db_connect.php'); // Ensure this is correct and works

// Check for successful database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set headers for CSV export
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=doctors_list.csv');

// Open output stream
$output = fopen('php://output', 'w');

// Output column headers
fputcsv($output, ['Full Name', 'Email', 'Specialization', 'Clinic Name', 'Location']);

// Fetch doctor data
$query = "SELECT name, email, specialization, clinic_name, clinic_location FROM Doctors";
$doctors = $conn->query($query);

// Check if the query was successful
if ($doctors === false) {
    echo "Error fetching data: " . $conn->error;
    exit();
}

// Check if there are any rows returned
if ($doctors->num_rows > 0) {
    // Loop through the doctor data and write it to the CSV
    while ($doctor = $doctors->fetch_assoc()) {
        fputcsv($output, $doctor);
    }
} else {
    echo "No doctors found.";
}

// Close the file stream
fclose($output);
exit();
?>
