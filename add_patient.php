<?php 
require_once 'db_connect.php'; // Ensure database connection is included

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $age = $_POST['age']; // Age instead of DOB
    $languages = $_POST['languages'];
    $weight = $_POST['weight']; // Added weight field

    // Handle Profile Picture Upload
    $profile_picture = null; // Default value if no image is uploaded
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $upload_dir = "uploads/patients/"; // Fixed directory for patient images

        // Ensure directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Generate a unique filename
        $profile_picture = time() . "_" . basename($_FILES["profile_picture"]["name"]);
        $upload_path = $upload_dir . $profile_picture;

        // Move uploaded file
        if (!move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $upload_path)) {
            die("Error uploading profile picture.");
        }
    }

    // Insert into Users table
    $stmt1 = $conn->prepare("INSERT INTO Users (full_name, email, phone_number, profile_picture, user_type) VALUES (?, ?, ?, ?, 'patient')");
    $stmt1->bind_param("ssss", $full_name, $email, $phone, $profile_picture);

    if (!$stmt1->execute()) {
        die("Error inserting into Users table: " . $stmt1->error);
    }

    // Get the generated user ID
    $user_id = $conn->insert_id;

    // Insert into Patients table
    $stmt2 = $conn->prepare("INSERT INTO Patients (id, full_name, email, phone, profile_picture, gender, age, weight, languages) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt2->bind_param("isssssids", $user_id, $full_name, $email, $phone, $profile_picture, $gender, $age, $weight, $languages);

    if ($stmt2->execute()) {
        echo "Patient added successfully!";
        header("Location: admin.php"); // Redirect back to admin panel
        exit();
    } else {
        die("Error inserting into Patients table: " . $stmt2->error);
    }
} else {
    die("Invalid request.");
}
?>
