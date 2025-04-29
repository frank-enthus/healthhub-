<?php
// Database connection
include('db_connect.php'); // Make sure you include your DB connection file

// Set headers for CSV export
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=patients_list.csv');

// Open output stream
$output = fopen('php://output', 'w');

// Output column headers
fputcsv($output, ['Full Name', 'Email', 'Phone', 'Gender', 'Age', 'Weight', 'Languages']);

// Fetch patient data
$query = "SELECT full_name, email, phone, gender, age, weight, languages FROM Patients";
$patients = $conn->query($query);

// Check if the query was successful
if ($patients === false) {
    echo "Error fetching data: " . $conn->error;
    exit();
}

// Check if there are any rows returned
if ($patients->num_rows > 0) {
    // Loop through the patient data and write it to the CSV
    while ($patient = $patients->fetch_assoc()) {
        fputcsv($output, $patient);
    }
} else {
    echo "No patients found.";
}

// Close the file stream
fclose($output);
exit();
?>
