<?php
require 'db_connect.php'; // Include database connection

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid hospital ID.");
}

$hospital_id = $_GET['id'];

// Fetch hospital details
$query = $conn->prepare("SELECT name, location, specialties, image, website, description, contact, services FROM hospitals WHERE id = ?");
$query->bind_param("i", $hospital_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    die("Hospital not found.");
}

$hospital = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($hospital['name']); ?> - Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/hospital detail.css"> <!-- Link external CSS -->
</head>
<body>
     <header>
        <div class="header-content">
            <img src="images/hh.jpg" alt="Clinic Logo" class="logo">
            <h1>HOSPITAL PROFILE</h1>
        </div>
    </header>
    <!-- Hero Section -->
    <div class="hero" style="background: url('<?php echo htmlspecialchars($hospital['image'] ?: 'default-hospital.jpg'); ?>') center/cover no-repeat;">
    <div class="overlay"></div>
    <div class="hero-content">
        <h1><?php echo htmlspecialchars($hospital['name']); ?></h1>
        <p><?php echo htmlspecialchars($hospital['location']); ?> - <?php echo htmlspecialchars($hospital['specialties']); ?></p>
    </div>
</div>

    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-4">
                <div class="sidebar">
                    <h4>Contact</h4>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($hospital['contact']); ?></p>
                    <?php if (!empty($hospital['website'])): ?>
                        <a href="<?php echo htmlspecialchars($hospital['website']); ?>" target="_blank" class="btn-visit">Visit Website</a>
                    <?php endif; ?>
                    <a href="HH homepage.php" class="btn-home">Back to Homepage</a>
                </div>
            </div>
            <!-- Main Content -->
            <div class="col-md-8">
                <div class="card">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($hospital['description'])); ?></p>
                    <h3>Services Offered</h3>
                    <p><?php echo nl2br(htmlspecialchars($hospital['services'])); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center text-white bg-primary mt-5 p-3">
        © <?php echo date("Y"); ?> HealthHub - All Rights Reserved
    </footer>
</body>
</html>

<?php
$conn->close();
?>
