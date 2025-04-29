<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = $_SESSION['doctor_id'];
    $day_of_week = $_POST['day_of_week'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $status = $_POST['status'];

    // Prevent duplicate schedules
    $checkQuery = "SELECT id FROM doctor_schedule WHERE doctor_id = ? AND day_of_week = ? AND start_time = ? AND end_time = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("isss", $doctor_id, $day_of_week, $start_time, $end_time);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        header("Location: doctor-dashboard.php?error=Schedule already exists");
        exit();
    }

    // Insert new schedule
    $query = "INSERT INTO doctor_schedule (doctor_id, day_of_week, start_time, end_time, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issss", $doctor_id, $day_of_week, $start_time, $end_time, $status);

    if ($stmt->execute()) {
        header("Location: doctor-dashboard.php?msg=Schedule added successfully");
        exit();
    } else {
        header("Location: doctor-dashboard.php?error=Failed to add schedule");
        exit();
    }
}
?>
