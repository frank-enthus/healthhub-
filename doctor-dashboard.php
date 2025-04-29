<?php
session_start();
include('db_connect.php');


// Ensure the doctor is logged in
if (!isset($_SESSION['doctor_id']) ) {
    header("Location: doctor.php");
    exit();
}

// Get Doctor Details
$doctor_id = $_SESSION['doctor_id'];
$user_id = $_SESSION['user_id'];
$query = "SELECT u.full_name, u.email, u.profile_picture, d.specialization, d.clinic_name 
          FROM Users u
          JOIN Doctors d ON u.id = d.user_id
          WHERE u.id = ?";


$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

// Fetch today's appointments
$today = date('Y-m-d');
$appointments_query = "SELECT p.full_name AS patient_name, p.gender, p.weight, p.age, a.appointment_time, a.status
                       FROM Appointments a
                       JOIN Patients p ON a.patient_name = p.id
                       WHERE a.doctor_id = ? AND a.appointment_date = ?";
$stmtAppointments = $conn->prepare($appointments_query);
$stmtAppointments->bind_param("is", $doctor_id, $today);
$stmtAppointments->execute();
$appointments = $stmtAppointments->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="styles/doctor-dashboard.css">
     <link rel="stylesheet" href="styles/doctor model.css">
    <script src="script/doctor-dashboard.js"></script>
    <script src="script/doctor model.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

    <!-- Main Container -->
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <img src="../healthhub project/images/hh.jpg" alt="Logo" class="logo" />
            <h2>Doctor Menu</h2>
            <ul>
                <li><a href="HH homepage.php"><i class="fa-solid fa-house"></i> Home</a></li>
                <li><a href="appointments1.php"><i class="fa-solid fa-calendar-check"></i> Appointments</a></li>
                <li><a href="doctor_messages.php"><i class="fa-solid fa-envelope"></i> Messages</a></li>
                <li><a href="edit_doctor_profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
                <li><a href="logout2.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Section -->
            <div class="top-section">
                <div class="bg-image">
                    <h1>Welcome, Dr. <?php echo htmlspecialchars($doctor['full_name']); ?></h1>
                </div>
            </div>

            <!-- Patient List -->
            <div class="patient-list">
                <h2>Today's Patients</h2>
                <div class="table-container">
                    <table class="patient-table">
                        <thead>
                            <tr>
                                <th>Patient Name</th>
                                <th>Gender</th>
                                <th>Weight</th>
                                <th>Age</th>
                                <th>Appointment Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($appointment = $appointments->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['gender']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['weight']); ?>kg</td>
                                    <td><?php echo htmlspecialchars($appointment['age']); ?> years</td>
                                    <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Section (Doctor's Profile, Appointments, Messages) -->
            <div class="right-section">
                <!-- Profile Section -->
                <div class="profile">
                    <img src="<?php echo $doctor['profile_picture'] ? $doctor['profile_picture'] : '../healthhub project/images/doctor.png'; ?>" alt="Doctor's Profile Picture">
                    <h3><?php echo htmlspecialchars($doctor['full_name']); ?></h3>
                    <p>Specialization: <?php echo htmlspecialchars($doctor['specialization']); ?></p>
                    <p>Clinic: <?php echo htmlspecialchars($doctor['clinic_name'] ?? 'Not Specified'); ?></p>
                </div>

               <!-- Update Schedule Button -->
<button class="toggle-btn" onclick="openModal()">Update Schedule</button>

<!-- Modal Structure -->
<div id="scheduleModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Manage Your Availability</h3>

        <!-- Show Existing Schedules -->
        <h4>Current Schedules</h4>
        <table border="1">
            <tr>
                <th>Day</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php
            require_once "db_connect.php";
            $doctor_id = $_SESSION['doctor_id']; // Ensure doctor is logged in

            $query = $conn->prepare("SELECT id, day_of_week, start_time, end_time, status FROM doctor_schedule WHERE doctor_id = ?");
            $query->bind_param("i", $doctor_id);
            $query->execute();
            $result = $query->get_result();

            while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <form action="update_schedule.php" method="POST">
                        <input type="hidden" name="schedule_id" value="<?= $row['id'] ?>">
                        <td>
                            <select name="day_of_week">
                                <option value="Monday" <?= ($row['day_of_week'] == 'Monday') ? 'selected' : '' ?>>Monday</option>
                                <option value="Tuesday" <?= ($row['day_of_week'] == 'Tuesday') ? 'selected' : '' ?>>Tuesday</option>
                                <option value="Wednesday" <?= ($row['day_of_week'] == 'Wednesday') ? 'selected' : '' ?>>Wednesday</option>
                                <option value="Thursday" <?= ($row['day_of_week'] == 'Thursday') ? 'selected' : '' ?>>Thursday</option>
                                <option value="Friday" <?= ($row['day_of_week'] == 'Friday') ? 'selected' : '' ?>>Friday</option>
                                <option value="Saturday" <?= ($row['day_of_week'] == 'Saturday') ? 'selected' : '' ?>>Saturday</option>
                                <option value="Sunday" <?= ($row['day_of_week'] == 'Sunday') ? 'selected' : '' ?>>Sunday</option>
                            </select>
                        </td>
                        <td><input type="time" name="start_time" value="<?= $row['start_time'] ?>"></td>
                        <td><input type="time" name="end_time" value="<?= $row['end_time'] ?>"></td>
                        <td>
                            <select name="status">
                                <option value="Available" <?= ($row['status'] == 'Available') ? 'selected' : '' ?>>Available</option>
                                <option value="Unavailable" <?= ($row['status'] == 'Unavailable') ? 'selected' : '' ?>>Unavailable</option>
                            </select>
                        </td>
                        <td>
                            <button type="submit">Update</button>
                            <a href="delete_schedule.php?schedule_id=<?= $row['id'] ?>" 
                               onclick="return confirm('Are you sure you want to delete this schedule?')">❌ Delete</a>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Add New Schedule -->
        <h4>Add New Schedule</h4>
        <form action="add_schedule.php" method="POST">
            <input type="hidden" name="doctor_id" value="<?= $doctor_id ?>">
            <label>Day:</label>
            <select name="day_of_week" required>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
                <option value="Sunday">Sunday</option>
            </select>
            <label>Start Time:</label>
            <input type="time" name="start_time" required>
            <label>End Time:</label>
            <input type="time" name="end_time" required>
            <label>Status:</label>
            <select name="status" required>
                <option value="Available">Available</option>
                <option value="Unavailable">Unavailable</option>
            </select>
            <button type="submit">Add Schedule</button>
        </form>
    </div>
</div>



<div class="notification-bell" onclick="showNotifications()">
    🔔 <span id="notif-count" style="color: red; font-weight: bold;"></span>
</div>

<div id="notifications-popup" class="hidden">
    <h3>Notifications</h3>
    <ul id="notifications-list"></ul>
    <button onclick="markAllRead()">Mark All as Read</button>
</div>




                <!-- Referral Section -->
                <div class="referral-section">
                    <h3>Refer & Earn</h3>
                    <p>Invite your colleagues and earn rewards!</p>
                    <div class="referral-code">
                        <span id="referralCode">HEALTH123</span>
                        <button onclick="copyCode()">Copy</button>
                    </div>
                    <div class="share-buttons">
                        <button class="whatsapp">WhatsApp</button>
                        <button class="email">Email</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    document.getElementById("scheduleForm").addEventListener("submit", function(event) {
        let startTime = document.getElementById("start_time").value;
        let endTime = document.getElementById("end_time").value;

        if (startTime >= endTime) {
            alert("Error: Start time must be before end time!");
            event.preventDefault(); // Stop form submission
        }
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
