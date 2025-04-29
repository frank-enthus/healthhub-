<?php
session_start();
session_regenerate_id(true); // Prevent session fixation
include('db_connect.php'); // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if the user exists
    $query = "SELECT * FROM Users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Store session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_type'] = $user['user_type'];

            // Redirect based on user type
            if ($user['user_type'] === 'patient') {
                header("Location: patient-dashboard.php");
                exit();
            } elseif ($user['user_type'] === 'doctor') {
                header("Location: doctor_dashboard.php");
                exit();
            }
        } else {
            echo "<script>alert('Incorrect password. Try again.'); window.location.href = 'patient.php';</script>";
        }
    } else {
        echo "<script>alert('No account found with that email.'); window.location.href = 'patient.php';</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/login2.css">
</head>
<body>
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
        <h2>Hi, Welcome Back Patient</h2>
        <p>Please fill in your details to log in</p>

        <!-- Login Form -->
        <form action="patient.php" method="POST">
          <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
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
        <p class="signup-text">Don’t have an account? <a href="signuppatient.php">Sign Up</a></p>
      </div>
    </div>
  </div>
</body>
</html>
