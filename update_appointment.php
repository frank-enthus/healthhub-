<?php
file_put_contents("test_log.txt", "update_appointment.php was hit\n", FILE_APPEND);

session_start();
include 'db_connect.php';

ini_set('display_errors', 0); // Don't show errors to user
ini_set('log_errors', 1);     // Log them instead
error_reporting(E_ALL);       // Report everything to logs

header('Content-Type: application/json'); // Force JSON output

// Redirect if not logged in
if (!isset($_SESSION['doctor_id'])) {
    error_log("Unauthorized access attempt"); // Log error
    echo json_encode(["error" => "Unauthorized access"]);
    exit();
}

// Only accept POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Parse incoming JSON
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!isset($data['appointment_id'], $data['status'])) {
        error_log("Invalid request data: " . print_r($data, true)); // Log error
        echo json_encode(["error" => "Invalid request"]);
        exit();
    }

    $appointment_id = (int)$data['appointment_id'];
    $status = $data['status'];

    // Fetch patient_id from appointment
    $stmt_patient = $conn->prepare("SELECT patient_id FROM appointments WHERE id = ?");
    if (!$stmt_patient) {
        error_log("Failed to prepare statement: " . $conn->error); // Log error
        echo json_encode(["error" => "Failed to prepare query"]);
        exit();
    }
    $stmt_patient->bind_param("i", $appointment_id);
    $stmt_patient->execute();
    $stmt_patient->bind_result($patient_id);
    $stmt_patient->fetch();
    $stmt_patient->close();

    if (empty($patient_id)) {
        error_log("Appointment not found for ID: $appointment_id"); // Log error
        echo json_encode(["error" => "Appointment not found"]);
        exit();
    }

    // Update appointment status
    $stmt_update = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    if (!$stmt_update) {
        error_log("Failed to prepare update statement: " . $conn->error); // Log error
        echo json_encode(["error" => "Failed to prepare query"]);
        exit();
    }
    $stmt_update->bind_param("si", $status, $appointment_id);

    if ($stmt_update->execute()) {
        // Send notification
        $content = "Your appointment has been $status by the doctor.";
        $stmt_notif = $conn->prepare("INSERT INTO notifications (user_id, type, content) VALUES (?, 'appointment', ?)");
        if (!$stmt_notif) {
            error_log("Failed to prepare notification statement: " . $conn->error); // Log error
            echo json_encode(["error" => "Failed to send notification"]);
            exit();
        }
        $stmt_notif->bind_param("is", $patient_id, $content);
        $stmt_notif->execute();
        $stmt_notif->close();

        echo json_encode(["success" => "Appointment updated successfully!"]);
    } else {
        error_log("Failed to execute update statement: " . $stmt_update->error); // Log error
        echo json_encode(["error" => "Failed to update appointment."]);
    }

    $stmt_update->close();
    $conn->close();
    exit();
}

// Fallback: wrong method
echo json_encode(["error" => "Invalid request method"]);
exit();
