<?php
session_start();
include('db_connect.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $registration_number = mysqli_real_escape_string($conn, $_POST['registration_number']);
    $password = $_POST['password'];

    // Step 1: Check if the registration number exists in the Doctors table
    $query = "SELECT * FROM doctors WHERE registration_number = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $registration_number);
    mysqli_stmt_execute($stmt);
    $doctorResult = mysqli_stmt_get_result($stmt);

    if ($doctor = mysqli_fetch_assoc($doctorResult)) {
        // Step 2: Retrieve user details from Users table using doctor ID
        $userQuery = "SELECT * FROM users WHERE id = ?";
        $stmt2 = mysqli_prepare($conn, $userQuery);
        mysqli_stmt_bind_param($stmt2, "i", $doctor['user_id']); // Assuming doctors table has user_id column
        mysqli_stmt_execute($stmt2);
        $userResult = mysqli_stmt_get_result($stmt2);

        if ($user = mysqli_fetch_assoc($userResult)) {
            // Step 3: Verify password
            if (password_verify($password, $user['password'])) {
                // Successful login
                $_SESSION['doctor_id'] = $doctor['id'];
                $_SESSION['registration_number'] = $registration_number;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_type'] = "doctor"; // Add this line

                header("Location: doctor-dashboard.php"); // Redirect to doctor dashboard
                exit();
            } else {
                echo "<script>alert('Incorrect password!'); window.location.href = 'doctor.php';</script>";
            }
        } else {
            echo "<script>alert('No user account found for this doctor!'); window.location.href = 'doctor.php';</script>";
        }
    } else {
        echo "<script>alert('No doctor found with this registration number!'); window.location.href = 'doctor.php';</script>";
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doctor Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/login1.css">
</head>
<body>
    <script src="script/doctor.js"></script>

  <div class="container">
    <!-- Left Section -->
    <div class="left-section">
      <img src="../healthhub project/images/hospital-building.jpg" alt="Hospital Building" class="background-image">
      <div class="overlay-text">
        <h1>A Central Hub for Health Information and Services</h1>
      </div>
    </div>

    <!-- Right Section -->
    <div class="right-section">
      <div class="login-card">
        <!-- HealthHub Logo -->
        <img src="../healthhub project/images/hh.jpg" alt="HealthHub Logo" class="healthhub-logo">
        <h2>Hi, Welcome Back Doctor</h2>
        <p>Please fill in your details to log in</p>

        <!-- Login Form -->
        <form action="doctor.php" method="POST">
          <div class="input-group">
             <label for="registration_number">Registration number</label>
    <input type="text" id="registration_number" name="registration_number" placeholder="Enter your Registration number" required>
          </div>
          <div class="input-group password-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
            <span class="toggle-password">
              <i class="fas fa-eye"></i>
            </span>
          </div>
          <div class="options">
            <label>
              <input type="checkbox" name="remember"> Remember me
            </label>
            <a href="#" class="forgot-password">Forgot Password?</a>
          </div>
          <button type="submit" class="btn btn-primary">Sign In</button>
        </form>
        <p class="signup-text">Don’t have an account? <a href="signupdoctor.php">Sign Up</a></p>
        <div id="error-message" style="color: red; display: none;">Invalid login credentials.</div>
      </div>
    </div>
  </div>
</body>
</html>
