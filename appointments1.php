<?php
session_start();
include('db_connect.php');

// Redirect to login if doctor is not logged in
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

// Fetch pending appointments
$sql = "SELECT * FROM appointments WHERE doctor_id = ? AND status = 'Pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Appointments</title>
    <link rel="stylesheet" href="styles/Appointments.css">
</head>
<body>
    <h1>Pending Appointments</h1>
    <table>
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                    <td>
                        <button onclick="updateAppointment(<?php echo $appointment['id']; ?>, 'Confirmed')">Approve</button>
                        <button onclick="updateAppointment(<?php echo $appointment['id']; ?>, 'Cancelled')">Decline</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div>
        <a href="doctor-dashboard.php" class="back-button">← Back to dashboard</a>
        <h2>Past Appointments</h2>
        <button id="togglePastBtn" onclick="togglePastAppointments()">Show Past Appointments</button>
<div id="pastAppointmentsContainer" style="display: none;"></div>

    </div>
    
    <script>
function updateAppointment(appointmentId, status) {
    fetch("update_appointment.php", {
    // or if needed: fetch("healthhub%20project/update_appointment.php", {

        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ appointment_id: appointmentId, status: status })
    })
    .then(response => {
        // Check if response is okay (status 200)
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json(); // Parse JSON response
    })
    .then(data => {
        console.log("Server returned:", data);  // Logging the entire response
        if (data.success) {
            alert(data.success);
            location.reload(); // Refresh to reflect changes
        } else {
            alert("Error: " + (data.error || "Unknown error"));
        }
    })
    .catch(error => {
        alert("Request failed: " + error);
    });
}




    </script>
    <script>
        let pastLoaded = false;
function togglePastAppointments() {
    const container = document.getElementById("pastAppointmentsContainer");
    const button = document.getElementById("togglePastBtn");

    if (container.style.display === "none") {
        container.style.display = "block";
        button.textContent = "Hide Past Appointments";

        if (!pastLoaded) {
            fetch('get_past_appointments.php')
                .then(response => response.text())
                .then(data => {
                    container.innerHTML = data;
                    pastLoaded = true;
                })
                .catch(error => {
                    container.innerHTML = "Error loading past appointments.";
                });
        }
    } else {
        container.style.display = "none";
        button.textContent = "Show Past Appointments";
    }
}


    </script>
</body>
</html>
