<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = $_SESSION['doctor_id']; // Ensure the doctor is logged in
    $day_of_week = $_POST['day_of_week'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $status = $_POST['status'];
    $schedule_id = $_POST['schedule_id'] ?? null; // Check if schedule_id is set

    if ($schedule_id) {
        // Update existing schedule
        $query = "UPDATE doctor_schedule SET day_of_week=?, start_time=?, end_time=?, status=? WHERE id=? AND doctor_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssii", $day_of_week, $start_time, $end_time, $status, $schedule_id, $doctor_id);
    } else {
        // Insert new schedule
        $query = "INSERT INTO doctor_schedule (doctor_id, day_of_week, start_time, end_time, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issss", $doctor_id, $day_of_week, $start_time, $end_time, $status);
    }

    if ($stmt->execute()) {
        echo "Schedule updated successfully.";
    } else {
        echo "Error updating schedule.";
    }

    $stmt->close();
    $conn->close();

    header("Location: doctor-dashboard.php");
    exit();
}
?>
