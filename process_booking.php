<?php
session_start();
include "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['doctor_id']) || empty($_POST['doctor_id'])) {
        $_SESSION['error'] = "Error: Missing doctor_id.";
        header("Location: book_appointment.php");
        exit();
    }

    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Error: You must be logged in to book an appointment.";
        header("Location: patient.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $doctor_id = intval($_POST['doctor_id']);
    $patient_name = trim($_POST['patient_name']);
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $status = "Pending";

    // ✅ Get patient ID using user's email
    $userQuery = "SELECT email FROM users WHERE id = ?";
    $userStmt = $conn->prepare($userQuery);
    $userStmt->bind_param("i", $user_id);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    $userRow = $userResult->fetch_assoc();

    if ($userRow) {
        $patient_email = $userRow['email'];

        $patientQuery = "SELECT id FROM patients WHERE email = ?";
        $patientStmt = $conn->prepare($patientQuery);
        $patientStmt->bind_param("s", $patient_email);
        $patientStmt->execute();
        $patientResult = $patientStmt->get_result();
        $patientRow = $patientResult->fetch_assoc();

        if ($patientRow) {
            $patient_id = $patientRow['id'];
        } else {
            $_SESSION['error'] = "Error: Patient record not found.";
            header("Location: book_appointment.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Error: User not found.";
        header("Location: book_appointment.php");
        exit();
    }

    // ✅ Check if doctor exists in users table
    $getDoctorUserIdQuery = "SELECT user_id FROM doctors WHERE id = ?";
    $getDoctorUserIdStmt = $conn->prepare($getDoctorUserIdQuery);
    $getDoctorUserIdStmt->bind_param("i", $doctor_id);
    $getDoctorUserIdStmt->execute();
    $doctorUserResult = $getDoctorUserIdStmt->get_result();
    
    if ($doctorUserResult->num_rows > 0) {
        $doctorUserRow = $doctorUserResult->fetch_assoc();
        $doctor_user_id = $doctorUserRow['user_id'];
    } else {
        $_SESSION['error'] = "Error: Doctor user ID not found.";
        header("Location: book_appointment.php?doctor_id=" . $doctor_id);
        exit();
    }

    // ✅ Check doctor availability
    $availabilityQuery = "SELECT start_time, end_time FROM doctor_schedule WHERE doctor_id = ? AND status = 'Available'";
    $availabilityStmt = $conn->prepare($availabilityQuery);
    $availabilityStmt->bind_param("i", $doctor_id);
    $availabilityStmt->execute();
    $availabilityResult = $availabilityStmt->get_result();

    $isAvailable = false;
    while ($availability = $availabilityResult->fetch_assoc()) {
        $start_time = date("H:i", strtotime($availability['start_time']));
        $end_time = date("H:i", strtotime($availability['end_time']));
        $selected_time = date("H:i", strtotime($appointment_time));

        if ($selected_time >= $start_time && $selected_time <= $end_time) {
            $isAvailable = true;
            break;
        }
    }

    if (!$isAvailable) {
        $_SESSION['error'] = "Doctor is not available at this time. Please choose another time.";
        header("Location: book_appointment.php?doctor_id=" . $doctor_id);
        exit();
    }

    // ✅ Check for existing appointment at that time
    $checkQuery = "SELECT id FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("iss", $doctor_id, $appointment_date, $appointment_time);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $_SESSION['error'] = "This time slot is already booked. Please choose another time.";
        header("Location: book_appointment.php?doctor_id=" . $doctor_id);
        exit();
    }

    // ✅ Insert the appointment
    $insertQuery = "INSERT INTO appointments (doctor_id, patient_id, patient_name, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("iissss", $doctor_id, $patient_id, $patient_name, $appointment_date, $appointment_time, $status);

    if ($insertStmt->execute()) {
        // ✅ Insert Notification using correct doctor user ID
        $content = "You have a new appointment request from " . htmlspecialchars($patient_name);
        $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, type, content) VALUES (?, 'appointment', ?)");
        $notif_stmt->bind_param("is", $doctor_user_id, $content);
        $notif_stmt->execute();
        $notif_stmt->close();

        $_SESSION['success'] = "Your appointment request has been sent. Please wait for confirmation.";
        echo "<script>
                alert('Your appointment request has been sent. Please wait for confirmation.');
                window.location.href = 'patient-dashboard.php';
              </script>";
        exit();
    } else {
        $_SESSION['error'] = "Error: Could not book appointment. " . $insertStmt->error;
        header("Location: book_appointment.php?doctor_id=" . $doctor_id);
        exit();
    }
}
?>
b