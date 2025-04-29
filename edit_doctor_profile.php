<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['doctor_id']) || !isset($_SESSION['user_id'])) {
    header("Location: doctor.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];
$user_id = $_SESSION['user_id'];

// Fetch existing doctor details
$query = "SELECT u.full_name, u.email, u.profile_picture, u.phone_number, d.specialization, d.clinic_name, d.clinic_location, d.bio, d.experience, d.schedule
          FROM Users u
          JOIN Doctors d ON u.id = d.user_id
          WHERE u.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $specialization = trim($_POST['specialization']);
    $clinic_name = trim($_POST['clinic_name']);
    $clinic_location = trim($_POST['clinic_location']);
    $bio = trim($_POST['bio']);
    $experience = trim($_POST['experience']);
    $schedule = trim($_POST['schedule']);
    $phone_number = trim($_POST['phone_number']);

    // Handle profile picture upload
    if (!empty($_FILES['profile_picture']['name'])) {
        $upload_dir = "uploads/";
        $filename = time() . "_" . basename($_FILES["profile_picture"]["name"]);
        $profile_picture = $upload_dir . $filename;
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $profile_picture);
    } else {
        $profile_picture = $doctor['profile_picture'];
    }

    // Update doctor info
    $update_query = "UPDATE Doctors SET specialization = ?, clinic_name = ?, clinic_location = ?, bio = ?, experience = ?, schedule = ? WHERE user_id = ?";
    $stmtUpdate = $conn->prepare($update_query);
    $stmtUpdate->bind_param("ssssssi", $specialization, $clinic_name, $clinic_location, $bio, $experience, $schedule, $user_id);
    
    if ($stmtUpdate->execute()) {
        // Update profile picture and phone number in Users table
        $stmtUpdateUser = $conn->prepare("UPDATE Users SET profile_picture = ?, phone_number = ? WHERE id = ?");
        $stmtUpdateUser->bind_param("ssi", $profile_picture, $phone_number, $user_id);
        $stmtUpdateUser->execute();

        echo "<script>alert('Profile updated successfully!'); window.location.href = 'doctor-dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating profile. Please try again.'); window.location.href = 'edit_doctor_profile.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Doctor Profile</title>
    <link rel="stylesheet" href="styles/doctor-profile.css">
</head>
<body>
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-image">
                <img src="<?php echo $doctor['profile_picture'] ? $doctor['profile_picture'] : 'default-doctor.png'; ?>" alt="Doctor's Profile Picture">
            </div>
            <h2><?php echo htmlspecialchars($doctor['full_name']); ?></h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($doctor['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($doctor['phone_number']); ?></p>
            <p><strong>Specialization:</strong> <?php echo htmlspecialchars($doctor['specialization']); ?></p>
            <p><strong>Clinic:</strong> <?php echo htmlspecialchars($doctor['clinic_name']); ?></p>
            <p><strong>Clinic Location:</strong> <?php echo htmlspecialchars($doctor['clinic_location']); ?></p>
            <p><strong>Experience:</strong> <?php echo htmlspecialchars($doctor['experience']); ?> years</p>
            <p><strong>schedule:</strong> <?php echo htmlspecialchars($doctor['schedule']); ?></p>
            <p><strong>Biography:</strong> <?php echo htmlspecialchars($doctor['bio']); ?></p>
        </div>

        <div class="profile-form">
            <h3>Edit Profile</h3>
            <form action="edit_doctor_profile.php" method="POST" enctype="multipart/form-data">
                <label for="specialization">Specialization</label>
                <input type="text" id="specialization" name="specialization" value="<?php echo htmlspecialchars($doctor['specialization']); ?>" required>

                <label for="clinic_name">Clinic Name</label>
                <input type="text" id="clinic_name" name="clinic_name" value="<?php echo htmlspecialchars($doctor['clinic_name']); ?>">

                <label for="clinic_location">Clinic Location</label>
                <input type="text" id="clinic_location" name="clinic_location" value="<?php echo htmlspecialchars($doctor['clinic_location']); ?>">

                <label for="bio">Biography</label>
                <textarea id="bio" name="bio" rows="3"><?php echo htmlspecialchars($doctor['bio']); ?></textarea>

                <label for="experience">Experience (Years)</label>
                <input type="number" id="experience" name="experience" value="<?php echo htmlspecialchars($doctor['experience']); ?>">

                <label for="availability">Schedule</label>
                <input type="text" id="schedule" name="schedule" value="<?php echo htmlspecialchars($doctor['schedule']); ?>">

                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($doctor['phone_number']); ?>">

                <label for="profile_picture">Profile Picture</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">

                <button type="submit" class="update-btn">Update Profile</button>
            </form>
        </div>
        
        <a href="doctor-dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>
