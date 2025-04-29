<?php
session_start();
include('db_connect.php'); // Ensure database connection
// Redirect to login if user is not logged in or is not a patient
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: patient.php");
    exit();
}



// Fetch patient's full name from the patients table using email
$email = $_SESSION['email'];
$query = "SELECT full_name FROM patients WHERE email = ?"; // Fetch full_names using email
$stmt = $conn->prepare($query);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $patient = $result->fetch_assoc();
    $patient_name = $patient['full_name']; // Get the patient's full name
    
} else {
    echo "<script>alert('Patient record not found.'); window.location.href = 'login.php';</script>";
    exit();
}

// Fetch all appointments for the patient using their full name
$sql = "SELECT a.*, u.full_name AS doctor_name 
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        JOIN users u ON d.user_id = u.id
        WHERE LOWER(TRIM(a.patient_name)) = LOWER(TRIM('$patient_name')) -- Normalize comparison
        ORDER BY a.appointment_date, a.appointment_time";


$result = $conn->query($sql);

if ($result === false) {
    die("Error fetching appointments: " . $conn->error);
}

$appointments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    
   
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <link rel="stylesheet" href="styles/patient_appointments.css">
</head>
<body>
    <h1>My Appointments</h1>
    <table>
        <thead>
            <tr>
                <th>Doctor Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?php echo $appointment['doctor_name']; ?></td>
                    <td><?php echo $appointment['appointment_date']; ?></td>
                    <td><?php echo $appointment['appointment_time']; ?></td>
                    <td>
                        <span class="status <?php echo strtolower($appointment['status']); ?>">
                            <?php echo $appointment['status']; ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($appointment['status'] === 'Pending' || $appointment['status'] === 'Confirmed'): ?>
                            <button class="cancel" onclick="cancelAppointment(<?php echo $appointment['id']; ?>)">Cancel</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div>
 <a href="patient-dashboard.php" class="back-button">← Back to dashboard</a>
 </div>

    <script>
        // Function to cancel an appointment
        function cancelAppointment(appointmentId) {
            if (confirm("Are you sure you want to cancel this appointment?")) {
                fetch('cancel_appointment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ appointment_id: appointmentId })
                })
                .then(response => response.text())
                .then(message => {
                    alert(message);
                    location.reload(); // Refresh the page to reflect changes
                });
            }
        }
    </script>
</body>
</html>