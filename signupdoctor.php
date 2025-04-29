<?php
require 'db_connect.php'; // Connect to the database

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure all inputs are sanitized
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name'] ?? "");
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? "");
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number'] ?? "");
    $password = password_hash($_POST['password'] ?? "", PASSWORD_BCRYPT);
    $registration_number = mysqli_real_escape_string($conn, $_POST['registration_number'] ?? "");
    $specialization = mysqli_real_escape_string($conn, $_POST['specialization'] ?? "");
    $clinic_name = mysqli_real_escape_string($conn, $_POST['clinic_name'] ?? "");
    $clinic_location = mysqli_real_escape_string($conn, $_POST['clinic_location'] ?? "");

    // Handle languages_spoken (if multiple are selected)
    $languages_spoken = isset($_POST["languages_spoken"]) ? implode(", ", $_POST["languages_spoken"]) : "";

    $referral_code = !empty($_POST['referral_code']) ? mysqli_real_escape_string($conn, $_POST['referral_code']) : NULL;

    // Ensure the uploads directory exists
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Handle Profile Picture Upload
    $profile_picture = "";
    if (!empty($_FILES["profile_picture"]["name"])) {
        $profile_picture = $upload_dir . time() . "_" . basename($_FILES["profile_picture"]["name"]);
        if (!move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $profile_picture)) {
            die("Error uploading profile picture.");
        }
    }

    // Handle Credentials Upload
    $credentials = "";
    if (!empty($_FILES["credentials"]["name"])) {
        $credentials = $upload_dir . time() . "_" . basename($_FILES["credentials"]["name"]);
        if (!move_uploaded_file($_FILES["credentials"]["tmp_name"], $credentials)) {
            die("Error uploading credentials.");
        }
    }

    // Insert into Users Table
    $sql_users = "INSERT INTO users (full_name, email, phone_number, password, profile_picture, user_type) 
                  VALUES ('$full_name', '$email', '$phone_number', '$password', '$profile_picture', 'doctor')";

    if (mysqli_query($conn, $sql_users)) {
        $user_id = mysqli_insert_id($conn); // Get the inserted user's ID

        // Insert into Doctors Table
        $sql_doctors = "INSERT INTO doctors (user_id, registration_number, specialization, clinic_name, clinic_location, languages_spoken, credentials, referral_code) 
                        VALUES ('$user_id', '$registration_number', '$specialization', '$clinic_name', '$clinic_location', '$languages_spoken', '$credentials', '$referral_code')";

        if (mysqli_query($conn, $sql_doctors)) {
            echo "<script>
                    alert('Doctor registered successfully!');
                    window.location.href = 'doctor.php'; // Redirect to dashboard or any other page
                  </script>";
        } else {
            echo "Error inserting into doctors table: " . mysqli_error($conn);
        }
    } else {
        echo "Error inserting into users table: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doctor Signup</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/signup.css">
</head>
<body>
  <div class="container">
     <form action="signupdoctor.php" method="POST" enctype="multipart/form-data">
    <!-- Left Section: Signup Form -->
    <div class="left-section">
      <div class="signup-card">
        <h2>Join as a Doctor</h2>
        <p>Fill in your details to connect with patients and build your practice.</p>
        <form id="doctor-signup-form" action="signupdoctor.php" method="POST" enctype="multipart/form-data">

          <!-- Personal Details -->
          <div class="input-group">
            <label for="full-name">Full Name</label>
            <input type="text" id="full-name" name="full_name" placeholder="Enter your full name" required>
          </div>

          <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
          </div>

          <div class="input-group">
            <label for="phone">Phone</label>
            <input type="tel" id="phone" name="phone_number" placeholder="Enter your phone number">
          </div>

          <div class="input-group">
            <label for="profile-picture">Profile Picture</label>
            <input type="file" id="profile-picture" name="profile_picture" accept="image/*">
          </div>

          <!-- Professional Details -->
          <div class="input-group">
            <label for="registration-number">Registration Number</label>
            <input type="text" id="registration-number" name="registration_number" placeholder="E.g., DMC-1234" required>
          </div>

          <div class="input-group">
            <label for="specialization">Specialization</label>
            <input type="text" id="specialization" name="specialization" placeholder="E.g., Pediatrics" required>
          </div>

          <div class="input-group">
            <label for="clinic-name">Clinic/Hospital Name</label>
            <input type="text" id="clinic-name" name="clinic_name" placeholder="Enter facility name">
          </div>

          <div class="input-group">
            <label for="clinic-location">Clinic Location</label>
            <input type="text" id="clinic-location" name="clinic_location" placeholder="Enter location">
          </div>

          <div class="input-group">
            <label for="credentials">Upload Credentials</label>
            <input type="file" id="credentials" name="credentials" accept=".pdf,.jpg,.png" required>
          </div>

          <div class="input-group">
            <label for="languages">Languages Spoken</label>
            <input type="text" id="languages" name="languages" placeholder="E.g., English, French">
          </div>

          <div class="input-group">
            <label for="password">Password</label>
             <input type="password" id="password" name="password" placeholder="Enter a strong password" required>
          </div>

           <div class="input-group">
             <label for="referral-code">Referral Code (Optional):</label>
             <input type="text" id="referral-code" name="referral_code" placeholder="Enter referral code">
          </div>

          <button type="submit" class="btn-primary">Sign Up</button>
        </form>
      </div>
    </div>

    <!-- Right Section: Background Image -->
    <div class="right-section">
      <img src="../healthhub project/images/signup-doctor.jpg" alt="Doctor Signup" class="background-image">
      <div class="overlay-text">
        <h1>Join Our Team</h1>
        <p>Help patients and grow your career.</p>
      </div>
    </div>
  </div>
  <!-- Modal -->
  <div id="confirmation-modal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Registration Successful!</h2>
      <p>Thank you for signing up. We will get back to you shortly.</p>
    </div>
  </div>

  <script src="script/modal2.js"></script>
</body>
</body>
</html>
