<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_POST['schedule_id'])) {
        $schedule_id = $_POST['schedule_id']; // If coming from a form
    } elseif (isset($_GET['schedule_id'])) {
        $schedule_id = $_GET['schedule_id']; // If coming from a link/button
    } else {
        die("No schedule ID provided.");
    }

    // Prepare and execute DELETE query
    $query = "DELETE FROM doctor_schedule WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $schedule_id);

    if ($stmt->execute()) {
        echo "Schedule removed.";
    } else {
        echo "Error deleting schedule.";
    }

    $stmt->close();
    $conn->close();

    // Redirect back after deletion
    header("Location: doctor-dashboard.php");
    exit();
} else {
    die("Invalid request method.");
}
?>
