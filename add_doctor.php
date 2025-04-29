<?php
session_start();
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get input values safely
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $registration_number = $_POST['registration_number'] ?? '';
    $specialization = $_POST['specialization'] ?? '';
    $clinic_name = $_POST['clinic_name'] ?? '';
    $clinic_location = $_POST['clinic_location'] ?? '';
    $user_type = 'doctor';

    // Handle Profile Picture Upload
    $profile_picture = "";
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $upload_dir = "uploads/doctors/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Ensure directory exists
        }

        $profile_picture = $upload_dir . time() . "_" . basename($_FILES["profile_picture"]["name"]); // Unique filename
        if (!move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $profile_picture)) {
            die("Error uploading profile picture.");
        }
    }

    if (!empty($full_name) && !empty($email) && !empty($registration_number) && !empty($specialization)) {
        $conn->begin_transaction(); // Start transaction

        try {
            // Insert into Users table
            $sql_users = "INSERT INTO Users (full_name, email, phone_number, profile_picture, user_type) VALUES (?, ?, ?, ?, ?)";
            $stmt_users = $conn->prepare($sql_users);
            $stmt_users->bind_param("sssss", $full_name, $email, $phone_number, $profile_picture, $user_type);

            if ($stmt_users->execute()) {
                $user_id = $stmt_users->insert_id; // Get the inserted User ID

                // Insert into Doctors table
                $sql_doctors = "INSERT INTO Doctors (user_id, registration_number, specialization, clinic_name, clinic_location) 
                                VALUES (?, ?, ?, ?, ?)";
                $stmt_doctors = $conn->prepare($sql_doctors);
                $stmt_doctors->bind_param("issss", $user_id, $registration_number, $specialization, $clinic_name, $clinic_location);

                if ($stmt_doctors->execute()) {
                    $conn->commit(); // Commit transaction
                    echo "Doctor added successfully!";
                    header("Location: admin.php");
                    exit();
                } else {
                    throw new Exception("Error inserting into Doctors table.");
                }
            } else {
                throw new Exception("Error inserting into Users table.");
            }
        } catch (Exception $e) {
            $conn->rollback(); // Rollback on error
            echo "Transaction failed: " . $e->getMessage();
        }

        $stmt_users->close();
        $stmt_doctors->close();
    } else {
        echo "All required fields must be filled.";
    }
}

$conn->close();
?>
