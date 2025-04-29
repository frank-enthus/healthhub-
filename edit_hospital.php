<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

$hospital_id = $_GET['id'];

// Fetch hospital details
$query = "SELECT * FROM Hospitals WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $hospital_id);
$stmt->execute();
$result = $stmt->get_result();
$hospital = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $contact = $_POST['contact'];
    $specialties = $_POST['specialties'];
    $services = $_POST['services'];
    $operating_hours = $_POST['operating_hours'];
    $website = $_POST['website'];
    $description = $_POST['description'];

    $update_query = "UPDATE Hospitals SET name=?, location=?, contact=?, specialties=?, services=?, operating_hours=?, website=?, description=? WHERE id=?";
    $stmtUpdate = $conn->prepare($update_query);
    $stmtUpdate->bind_param("ssssssssi", $name, $location, $contact, $specialties, $services, $operating_hours, $website, $description, $hospital_id);

    if ($stmtUpdate->execute()) {
        echo "<script>alert('Hospital updated successfully!'); window.location.href = 'admin.php';</script>";
    } else {
        echo "<script>alert('Error updating hospital.'); window.location.href = 'edit_hospital.php?id=$hospital_id';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Hospital</title>
    <link rel="stylesheet" href="styles/edit hospital.css">
</head>
<body>
    <div class="admin-container">
        <h2>Edit Hospital</h2>
        <form action="edit_hospital.php?id=<?php echo $hospital_id; ?>" method="POST">
            <input type="text" name="name" value="<?php echo $hospital['name']; ?>" required>
            <input type="text" name="location" value="<?php echo $hospital['location']; ?>" required>
            <input type="text" name="contact" value="<?php echo $hospital['contact']; ?>" required>
            <input type="text" name="specialties" value="<?php echo $hospital['specialties']; ?>">
            <input type="text" name="services" value="<?php echo $hospital['services']; ?>">
            <input type="text" name="operating_hours" value="<?php echo $hospital['operating_hours']; ?>">
            <input type="url" name="website" value="<?php echo $hospital['website']; ?>">
            <textarea name="description"><?php echo $hospital['description']; ?></textarea>
            <button type="submit">Update Hospital</button>
        </form>
    </div>
</body>
</html>
