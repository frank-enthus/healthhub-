<?php
// Database connection
include('db_connect.php'); // Make sure you include your DB connection file

// Set headers for CSV export
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=hospitals_list.csv');

// Open output stream
$output = fopen('php://output', 'w');

// Output column headers
fputcsv($output, ['Hospital Name', 'Location', 'Contact', 'Specialties', 'Services', 'Operating Hours']);

// Fetch hospital data
$query = "SELECT name, location, contact, specialties, services, operating_hours FROM Hospitals";
$hospitals = $conn->query($query);

// Check if the query was successful
if ($hospitals === false) {
    echo "Error fetching data: " . $conn->error;
    exit();
}

// Check if there are any rows returned
if ($hospitals->num_rows > 0) {
    // Loop through the hospital data and write it to the CSV
    while ($hospital = $hospitals->fetch_assoc()) {
        fputcsv($output, $hospital);
    }
} else {
    echo "No hospitals found.";
}

// Close the file stream
fclose($output);
exit();
?>
