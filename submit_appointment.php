<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = $_POST['doctor_id'];
    $patient_name = $_POST['patient_name'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];

    $insertQuery = $conn->prepare("INSERT INTO appointments (doctor_id, patient_name, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, 'Pending')");
    
    if ($insertQuery->execute([$doctor_id, $patient_name, $appointment_date, $appointment_time])) {
        echo "Appointment booked successfully!";
    } else {
        echo "Error booking appointment.";
    }
}
?>
