<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctor.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_id = $_POST['appointment_id'];
    $new_date = $_POST['new_date'];
    $new_time = $_POST['new_time'];

    // Update appointment in database
    $query = "UPDATE Appointments SET appointment_date = ?, appointment_time = ?, status = 'Rescheduled' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $new_date, $new_time, $appointment_id);

    if ($stmt->execute()) {
        echo "<script>alert('Appointment rescheduled successfully!'); window.location.href = 'doctor-dashboard.php';</script>";
    } else {
        echo "<script>alert('Error rescheduling appointment. Please try again.'); window.location.href = 'doctor-dashboard.php';</script>";
    }
}
?>
