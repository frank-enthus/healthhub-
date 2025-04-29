<?php
session_start();
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['doctor_id'])) {
    $doctor_id = intval($_POST['doctor_id']); // Get doctor ID safely

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get the user_id and profile picture before deletion
        $query = "SELECT u.id AS user_id, u.profile_picture FROM Users u 
                  JOIN Doctors d ON u.id = d.user_id 
                  WHERE d.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['user_id'];
            $profile_picture = $row['profile_picture'];

            // Delete doctor from Doctors table
            $stmt_delete_doctor = $conn->prepare("DELETE FROM Doctors WHERE id = ?");
            $stmt_delete_doctor->bind_param("i", $doctor_id);
            $stmt_delete_doctor->execute();

            // Delete user from Users table
            $stmt_delete_user = $conn->prepare("DELETE FROM Users WHERE id = ?");
            $stmt_delete_user->bind_param("i", $user_id);
            $stmt_delete_user->execute();

            // Remove profile picture from server
            if (!empty($profile_picture)) {
                $profile_path = "uploads/" . $profile_picture;
                if (file_exists($profile_path)) {
                    unlink($profile_path); // Delete file
                }
            }

            $conn->commit(); // Commit transaction
            echo "Doctor deleted successfully!";
            header("Location: admin.php"); // Redirect to admin panel
            exit();
        } else {
            throw new Exception("Doctor not found.");
        }
    } catch (Exception $e) {
        $conn->rollback(); // Rollback on error
        echo "Error: " . $e->getMessage();
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>

