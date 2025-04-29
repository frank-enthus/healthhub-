<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['doctor_id'])) {
    echo "Not authorized.";
    exit();
}

$doctor_id = $_SESSION['doctor_id'];
$today = date('Y-m-d');

// Join with patients table to show patient names
$sql = "SELECT a.*, p.full_name AS patient_name 
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        WHERE a.doctor_id = ? 
        AND (a.status = 'Confirmed' OR a.status = 'Completed') 
        AND a.appointment_date < ?
        ORDER BY a.appointment_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $doctor_id, $today);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>Patient Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['patient_name']) . "</td>
                <td>" . htmlspecialchars($row['appointment_date']) . "</td>
                <td>" . htmlspecialchars($row['appointment_time']) . "</td>
                <td>" . htmlspecialchars($row['status']) . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No past appointments found.";
}
echo "<button onclick=\"window.print()\">Print This Report</button>";

$stmt->close();
$conn->close();
?>
