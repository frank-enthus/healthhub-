<?php
session_start();
include('db_connect.php'); // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = isset($_POST['phone']) && trim($_POST['phone']) !== "" ? trim($_POST['phone']) : "NO_PHONE_" . time();
    $languages = trim($_POST['languages']);

    // Handle profile picture upload
    $profile_picture = NULL;
    if (!empty($_FILES['profile_picture']['name'])) {
        $upload_dir = "uploads/";
        $filename = time() . "_" . basename($_FILES["profile_picture"]["name"]);
        $profile_picture = $upload_dir . $filename;
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $profile_picture);
    }

    // Step 1: Check if email or phone already exists in Patients
    $checkUserQuery = "SELECT * FROM Patients WHERE email = ? OR phone = ?";
    $stmtCheck = $conn->prepare($checkUserQuery);
    $stmtCheck->bind_param("ss", $email, $phone);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        echo "<script>alert('Email or phone number already registered! Try another one.'); window.location.href = 'signuppatient.php';</script>";
        exit();
    }

    // Step 2: Insert into Users table
    $user_query = "INSERT INTO Users (full_name, email, phone_number, password, user_type) VALUES (?, ?, ?, ?, 'patient')";
    $stmtUser = $conn->prepare($user_query);
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmtUser->bind_param("ssss", $full_name, $email, $phone, $hashed_password);
    $stmtUser->execute();

    // Step 3: Insert into Patients table
    $query = "INSERT INTO Patients (full_name, email, phone, profile_picture, languages) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $full_name, $email, $phone, $profile_picture, $languages);

    if ($stmt->execute()) {
        echo "<script>alert('Signup successful!'); window.location.href = 'patient.php';</script>";
    } else {
        echo "<script>alert('Error signing up. Please try again.'); window.location.href = 'signuppatient.php';</script>";
    }
}

?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Signup</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/signup.css">
</head>
<body>
  <div class="container">
    <!-- Left Section: Signup Form -->
    <div class="left-section">
      <div class="signup-card">
        <h2>Join as a Patient</h2>
        <p>Fill in your details to connect with doctors and gain access to our services.</p>
      <form id="patient-signup-form" action="signuppatient.php" method="POST" enctype="multipart/form-data">
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
            <input type="tel" id="phone" name="phone" placeholder="Enter your phone number">
          </div>
          <div class="input-group">
            <label for="profile-picture">Profile Picture</label>
            <input type="file" id="profile-picture" name="profile_picture" accept="image/*">
          </div>
      
      
          <div class="input-group">
            <label for="languages">Languages Spoken</label>
            <input type="text" id="languages" name="languages" placeholder="E.g., English, French">
          </div>
          <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Create a password" required>
          </div>
          <div class="input-group">
            <label for="confirm-password">Confirm Password</label>
            <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm password" required>
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
        <p>Let us help you make a difference.</p>
      </div>
    </div>
  </div>
</body>
</html>
