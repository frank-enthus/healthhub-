<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hospital_id = $_POST['hospital_id'];

    $query = "DELETE FROM Hospitals WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $hospital_id);

    if ($stmt->execute()) {
        echo "<script>alert('Hospital deleted successfully!'); window.location.href = 'admin.php';</script>";
    } else {
        echo "<script>alert('Error deleting hospital. Please try again.'); window.location.href = 'admin.php';</script>";
    }
}
?>
