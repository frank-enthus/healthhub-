<?php
require 'db_connect.php'; // Include database connection

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid doctor ID.");
}

$doctor_id = $_GET['id'];

// Fetch doctor details along with user details
$query = $conn->prepare("
    SELECT u.full_name, u.profile_picture, d.specialization, d.bio, d.experience, 
           u.phone_number, d.schedule, d.languages_spoken, d.clinic_name, d.clinic_location 
    FROM doctors d 
    JOIN users u ON d.user_id = u.id 
    WHERE d.id = ?
");
$query->bind_param("i", $doctor_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    die("Doctor not found.");
}

$doctor = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile</title>
    <link rel="stylesheet" href="styles/doctor detail.css"> <!-- Link external CSS -->
</head>
<body>
    <header>
        <div class="header-content">
            <img src="images/hh.jpg" alt="Clinic Logo" class="logo">
            <h1>DOCTOR PROFILE</h1>
        </div>
    </header>
    <!-- Background Image -->
    <div class="background-image"></div>

<div class="profile-container">
    <!-- Left Section (Image & Bio) -->
    <div class="left-section">
        <img src="<?php echo htmlspecialchars($doctor['profile_picture']); ?>" alt="Doctor's Profile Picture" class="profile-img">
        
        <div class="bio-section">
            <h3>Biography</h3>
            <p><?php echo nl2br(htmlspecialchars($doctor['bio'])); ?></p>
        </div>
        <a href="HH homepage.php" class="back-button">← Back to homepage</a>
    </div>

    <!-- Right Section (Details) -->
    <div class="right-section">
        <!-- Name and Specialty -->
        <div class="profile-header">
            <h2><?php echo htmlspecialchars($doctor['full_name']); ?></h2>
            <p class="specialization"><?php echo htmlspecialchars($doctor['specialization']); ?></p>
        </div>

        <!-- Specialty Badges -->
        <div class="specialty-section">
            <?php 
            $specialties = explode(",", $doctor['specialization']); 
            foreach ($specialties as $specialty) {
                echo "<span class='badge'>" . htmlspecialchars(trim($specialty)) . "</span>";
            }
            ?>
        </div>

        <!-- Experience & Other Details -->
        <div class="details-grid">
            <div><strong>Experience:</strong> <?php echo htmlspecialchars($doctor['experience']); ?> years</div>
            <div><strong>Languages:</strong> <?php echo htmlspecialchars($doctor['languages_spoken']); ?></div>
            <div><strong>Clinic:</strong> <?php echo htmlspecialchars($doctor['clinic_name']); ?></div>
            <div><strong>Location:</strong> <?php echo htmlspecialchars($doctor['clinic_location']); ?></div>
            <div><strong>Schedule:</strong> <?php echo htmlspecialchars($doctor['schedule']); ?></div>
        </div>

        <!-- Contact -->
        <div class="contact-section">
            <h3>Contact</h3>
            <p><?php echo htmlspecialchars($doctor['phone_number']); ?></p>

        </div>
    </div>
</div>
</body>
</html>
