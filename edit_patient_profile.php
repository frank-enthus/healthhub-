<?php
session_start();
require 'db_connect.php'; // Ensure database connection

// Check if patient is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    die("Unauthorized access!");
}

$user_id = $_SESSION['user_id']; // Get logged-in patient ID

// Fetch patient details
$sql = "SELECT u.full_name, u.email, u.phone_number, u.profile_picture, 
               p.gender, p.weight, p.age, p.languages 
        FROM users u 
        JOIN patients p ON u.id = p.user_id 
        WHERE u.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if (!$patient) {
    die("Patient not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $gender = $_POST['gender'];
    $weight = isset($_POST['weight']) && is_numeric($_POST['weight']) ? floatval($_POST['weight']) : NULL;
    $age = isset($_POST['age']) && is_numeric($_POST['age']) ? intval($_POST['age']) : NULL;
    $languages = trim($_POST['languages']);

    // Handle profile picture upload
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "uploads/";
        $image_name = basename($_FILES["profile_picture"]["name"]);
        $target_file = $target_dir . time() . "_" . $image_name; 
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);
    } else {
        $target_file = $patient['profile_picture']; // Keep existing picture
    }

    // Update `users` table
    $sql = "UPDATE users SET full_name=?, phone_number=?, profile_picture=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $full_name, $phone, $target_file, $user_id);
    $stmt->execute();

    // Update `patients` table with profile picture
$sql = "UPDATE patients SET gender=?, weight=?, age=?, languages=?, profile_picture=? WHERE user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sdisss", $gender, $weight, $age, $languages, $target_file, $user_id);
$stmt->execute();

    echo "<script>alert('Profile updated successfully!'); window.location.href='edit_patient_profile.php';</script>";
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit patient Profile</title>
    <link rel="stylesheet" href="styles/patient-profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <h2>Edit Profile</h2>
    <form method="POST" enctype="multipart/form-data">
    <!-- Full Name -->
    <label><i class="fas fa-user"></i> Full Name:</label>
    <input type="text" name="full_name" value="<?= htmlspecialchars($patient['full_name']) ?>" required><br>

    <!-- Email -->
    <label><i class="fas fa-envelope"></i> Email (cannot change):</label>
    <input type="email" value="<?= htmlspecialchars($patient['email']) ?>" disabled><br>

    <!-- Phone Number -->
    <label><i class="fas fa-phone"></i> Phone Number:</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($patient['phone_number']) ?>" required><br>

    <!-- Gender -->
    <label><i class="fas fa-venus-mars"></i> Gender:</label>
    <select name="gender" required>
        <option value="Male" <?= $patient['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= $patient['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
        <option value="Other" <?= $patient['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
    </select><br>

    <!-- Weight -->
    <label><i class="fas fa-weight-hanging"></i> Weight (kg):</label>
    <input type="number" name="weight" value="<?= htmlspecialchars($patient['weight']) ?>" step="0.01"><br>

    <!-- Age -->
    <label><i class="fas fa-birthday-cake"></i> Age:</label>
    <input type="number" name="age" value="<?= htmlspecialchars($patient['age']) ?>"><br>

    <!-- Languages Spoken -->
    <label><i class="fas fa-language"></i> Languages Spoken:</label>
    <input type="text" name="languages" value="<?= htmlspecialchars($patient['languages']) ?>" required><br>

    <!-- Profile Picture -->
    <label><i class="fas fa-camera"></i> Profile Picture:</label>
    <input type="file" name="profile_picture"><br>
    <?php if ($patient['profile_picture']): ?>
        <img src="<?= htmlspecialchars($patient['profile_picture']) ?>" alt="Profile Picture" width="100">
    <?php endif; ?>
    <br>

    <!-- Submit Button -->
    <button type="submit"><i class="fas fa-save"></i> Update Profile</button>
    <a href="patient-dashboard.php" class="back-button">← Back to dashboard</a>
</form>
</body>
</html>
