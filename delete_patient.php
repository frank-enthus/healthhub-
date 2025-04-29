<?php
require_once 'db_connect.php'; // Ensure database connection is included

// Check if the request is a POST request and patient_id is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['patient_id'])) {
    $patient_id = $_POST['patient_id'];

    // Get profile picture filename before deleting the patient
    $stmt1 = $conn->prepare("SELECT profile_picture FROM Patients WHERE id = ?");
    $stmt1->bind_param("i", $patient_id);
    $stmt1->execute();
    $stmt1->bind_result($profile_picture);
    $stmt1->fetch();
    $stmt1->close();

    // Delete patient record from Patients table
    $stmt2 = $conn->prepare("DELETE FROM Patients WHERE id = ?");
    $stmt2->bind_param("i", $patient_id);
    if (!$stmt2->execute()) {
        die("Error deleting from Patients table: " . $stmt2->error);
    }
    $stmt2->close();

    // Delete user record from Users table
    $stmt3 = $conn->prepare("DELETE FROM Users WHERE id = ?");
    $stmt3->bind_param("i", $patient_id);
    if (!$stmt3->execute()) {
        die("Error deleting from Users table: " . $stmt3->error);
    }
    $stmt3->close();

    // Delete profile picture if it exists
    if (!empty($profile_picture)) {
        $image_path = "uploads/patients/" . $profile_picture;
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // Redirect back to admin panel after deletion
    header("Location: admin.php?message=Patient deleted successfully");
    exit();
} else {
    die("Invalid request.");
}
?>
