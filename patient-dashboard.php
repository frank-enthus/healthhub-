<?php
session_start();
include('db_connect.php');


// Redirect if not logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: patient.php");
    exit();
}

// Fetch patient details
$email = $_SESSION['email'];
$query = "SELECT * FROM Patients WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

// Fetch patient appointments
$appointments_query = "SELECT u.full_name AS doctor_name, a.appointment_date, a.appointment_time, a.status 
                       FROM Appointments a
                       JOIN Users u ON a.doctor_id = u.id
                       WHERE a.patient_name = ?";

$stmtAppointments = $conn->prepare($appointments_query);
$stmtAppointments->bind_param("s", $_SESSION['user_id']);
$stmtAppointments->execute();
$appointments = $stmtAppointments->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="styles/patient-dashboard.css">
    <link rel="stylesheet" href="styles/list.css">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<script src="script/search-hospitals.js"></script>
<script src="script/notifications.js"></script>
<link rel="stylesheet" href="styles/popup.css">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <img src="../healthhub project/images/hh.jpg" alt="HealthHub Logo">
                <h2>HealthHub</h2>
            </div>
            <ul>
                <li><a href="HH homepage.php"><i class="fa-solid fa-house"></i> Home</a></li>
                <li><a href="patient_messages.php"><i class="fa-solid fa-comments"></i> Messages</a></li>
                <li><a href="patient_appointments.php"><i class="fa-solid fa-calendar-check"></i> Appointments</a></li>
                <li><a href="edit_patient_profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
                <li><a href="logout1.php"><i class="fa-solid fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header>
                <div class="search-bar">
                    <input type="text" placeholder="Search...">
                </div>

                <div class="notification-bell" onclick="showNotifications()">
            🔔 <span id="notif-count" style="color: red; font-weight: bold;"></span>
                </div>

            <div id="notifications-popup" class="hidden">
          <h3>Notifications</h3>
         <ul id="notifications-list"></ul>
         <button onclick="markAllRead()">Mark All as Read</button>
            </div>
       </header>

            <!-- Patient Overview -->
            <section class="overview">
                <!-- Profile Card -->
                <div class="card profile-card">
                    <img src="<?php echo $patient['profile_picture'] ? $patient['profile_picture'] : '../healthhub project/images/user1.jpg'; ?>" alt="Profile Picture">
                    <h3><?php echo htmlspecialchars($patient['full_name']); ?></h3>
                    <p>Email: <?php echo htmlspecialchars($patient['email']); ?></p>
                    <p>Phone: <?php echo htmlspecialchars($patient['phone'] ?: 'Not provided'); ?></p>
                    <p>Languages: <?php echo htmlspecialchars($patient['languages']); ?></p>
                    <button onclick="window.location.href='edit_patient_profile.php'" class="view-profile">Edit Profile</button>
                </div>
<div class="card search-card doctor-card">
    <h3>Find a Doctor</h3>
   <div class="search-wrapper">
    <input type="text" id="doctor-search" placeholder="Find a Doctor by name or specialization">
    <div id="doctor-results"></div>
</div>

</div>





<div class="card search-card hospital-card">
    <h3>Find a Hospital</h3>
    <input type="text" id="hospital-search" placeholder="Search by name">
    <select id="location-filter">
        <option value="">Filter by location</option>
        <option value="Nairobi">Nairobi</option>
        <option value="Mombasa">Mombasa</option>
        <option value="Kisumu">Kisumu</option>
        <option value="kakamega">Kakamega</option>
        <option value="Kisii">Kisii</option>
        <option value="Murang'a">Murang'a</option>
        <option value="Nyeri">Nyeri</option>
        <option value="Busia">Busia</option>
        <option value="Machakos">Machakos</option>
    </select>
    <button class="search-btn" id="find-hospitals">Find Hospitals</button>

    <!-- Move the popup here -->
    <div id="hospital-popup" class="popup">
        <span class="close-btn">&times;</span>
        <ul id="hospital-results"></ul>
    </div>
</div>
</section>
            <!-- Google Maps -->
            <section id="map-container">
                <h2>Nearby Healthcare Facilities</h2>
                <input id="pac-input" type="text" placeholder="Search for a place">
                <div id="map"></div>
                <button id="locate-me">📍 Locate Me</button>
                <label for="travel-mode">Select Travel Mode: </label>
                    <select id="travel-mode">
                 <option value="DRIVING">🚗 Driving</option>
              <option value="WALKING">🚶 Walking</option>
              <option value="BICYCLING">🚲 Bicycling</option>
              <option value="TRANSIT">🚌 Transit</option>

    </select>
    </section>
           
        </main>
    </div>

   
    <script 
    async defer src="https://maps.gomaps.pro/maps/api/js?key=AlzaSyqYymK3knayVFCKImImtm3Y6qEbSf8H4Gm&libraries=places&callback=initMap">
</script>

<script src="script/map.js"></script>
<script src="script/search-doctors.js"></script>
<script>
document.getElementById("doctor-search").addEventListener("input", function() {
    let results = document.getElementById("doctor-results");
    if (this.value.length > 0) {
        results.style.display = "block"; // Show dropdown
    } else {
        results.style.display = "none"; // Hide dropdown if input is empty
    }
});
</script>

<script>
  // Fetch notifications every 5 seconds
function fetchNotifications() {
    fetch('fetch_notifications.php')
    .then(response => response.json())
    .then(data => {
        let notifList = document.getElementById("notifications-list");
        let notifCount = document.getElementById("notif-count");
        
        notifList.innerHTML = ""; // Clear old notifications
        if (data.length > 0) {
            notifCount.innerText = data.length; // Show count in bell icon
            
            data.forEach(notif => {
                let listItem = document.createElement("li");
                listItem.innerHTML = `${notif.content} <small>(${notif.created_at})</small>`;
                notifList.appendChild(listItem);
            });

            document.getElementById("notifications-popup").classList.remove("hidden");
        } else {
            notifCount.innerText = ""; // Hide count if no new notifications
        }
    });
}

// Mark all notifications as read
function markAllRead() {
    fetch('mark_notifications.php', { method: 'POST' })
    .then(() => {
        document.getElementById("notif-count").innerText = "";
        document.getElementById("notifications-list").innerHTML = "<li>No new notifications</li>";
    });
}

// Fetch notifications every 5 seconds
setInterval(fetchNotifications, 5000);
fetchNotifications(); // Fetch immediately on page load
</script>
</body>


</html>
