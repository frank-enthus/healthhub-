<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim and sanitize inputs
    $name = trim($_POST['name']);
    $location = trim($_POST['location']);
    $contact = trim($_POST['contact']);
    $specialties = trim($_POST['specialties']);
    $services = trim($_POST['services']);
    $operating_hours = trim($_POST['operating_hours']);
    $website = filter_var(trim($_POST['website']), FILTER_SANITIZE_URL);
    $description = trim($_POST['description']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);

    // Validate required fields
    if (empty($name) || empty($location) || empty($contact)) {
        echo "<script>alert('Hospital Name, Location, and Contact are required.'); window.history.back();</script>";
        exit();
    }

    // Validate website URL
    if (!empty($website) && !filter_var($website, FILTER_VALIDATE_URL)) {
        echo "<script>alert('Invalid website URL.'); window.history.back();</script>";
        exit();
    }

    // Handle Image Upload with Validation and Resizing
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($_FILES['image']['tmp_name']);

        if (!in_array($file_type, $allowed_types)) {
            echo "<script>alert('Only JPG, PNG, and GIF formats are allowed.'); window.history.back();</script>";
            exit();
        }

        $upload_dir = "uploads/hospitals/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $image_path = $upload_dir . $filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
            // Resize image to 300x200 pixels
            if (resizeImage($image_path, $image_path, 300, 200)) {
                $image = $image_path;
            } else {
                echo "<script>alert('Error processing image.'); window.history.back();</script>";
                exit();
            }
        } else {
            echo "<script>alert('Error uploading image.'); window.history.back();</script>";
            exit();
        }
    }

    // Insert into database
    $query = "INSERT INTO Hospitals (name, location, contact, specialties, services, operating_hours, website, description,  latitude, longitude,image) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssssss", $name, $location, $contact, $specialties, $services, $operating_hours, $website, $description, $latitude, $longitude, $image);

    if ($stmt->execute()) {
        echo "<script>alert('Hospital added successfully!'); window.location.href = 'admin.php';</script>";
    } else {
        echo "<script>alert('Error adding hospital. Please try again.'); window.history.back();</script>";
    }
}

// Function to resize image
function resizeImage($source, $destination, $new_width, $new_height) {
    list($width, $height, $type) = getimagesize($source);
    $image = null;

    switch ($type) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($source);
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($source);
            break;
        default:
            return false;
    }

    $new_image = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($new_image, $destination, 80);
            break;
        case IMAGETYPE_PNG:
            imagepng($new_image, $destination);
            break;
        case IMAGETYPE_GIF:
            imagegif($new_image, $destination);
            break;
    }

    imagedestroy($image);
    imagedestroy($new_image);
    return true;
}
?>
