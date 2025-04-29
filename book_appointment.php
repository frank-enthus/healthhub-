<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to book an appointment.");
}

$patient_id = intval($_SESSION['user_id']); // Ensure it's an integer

if (!isset($_GET['doctor_id']) || empty($_GET['doctor_id'])) {
    die("Error: Missing doctor_id in URL");
}

$doctor_id = intval($_GET['doctor_id']); // Ensure it's an integer

// Fetch doctor details
$sql = "SELECT users.full_name, users.profile_picture, doctors.specialization, doctors.clinic_name, doctors.bio, doctors.experience 
        FROM users
        INNER JOIN doctors ON users.id = doctors.user_id
        WHERE doctors.id = ? LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

if (!$doctor) {
    die("Error: Doctor not found.");
}

// Fetch available schedule
$scheduleQuery = $conn->prepare("SELECT day_of_week, start_time, end_time FROM doctor_schedule WHERE doctor_id = ? AND status = 'Available'");
$scheduleQuery->bind_param("i", $doctor_id);
$scheduleQuery->execute();
$scheduleResult = $scheduleQuery->get_result();

$schedule_data = [];
while ($row = $scheduleResult->fetch_assoc()) {
    $schedule_data[] = $row;
}

// Function to get upcoming dates
function getUpcomingDates($day_of_week, $weeks = 3) {
    $daysOfWeekMap = [
        "Monday" => 1, "Tuesday" => 2, "Wednesday" => 3,
        "Thursday" => 4, "Friday" => 5, "Saturday" => 6, "Sunday" => 7
    ];

    $upcomingDates = [];
    $currentDate = new DateTime();

    for ($i = 0; $i < ($weeks * 7); $i++) {
        $currentDate->modify("+1 day");
        if ($currentDate->format("l") === $day_of_week) {
            $upcomingDates[] = $currentDate->format("Y-m-d");
            if (count($upcomingDates) == $weeks) {
                break;
            }
        }
    }

    return $upcomingDates;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link rel="stylesheet" href="styles/book.css">
</head>
<body>
    <div class="container">
        <div class="doctor-profile">
            <img src="<?= htmlspecialchars($doctor['profile_picture']) ?>" alt="Doctor Profile Picture">
        </div>
        <div class="doctor-info">
            <h2>Dr. <?= htmlspecialchars($doctor['full_name']) ?></h2>
            <p><strong>Specialization:</strong> <?= htmlspecialchars($doctor['specialization']) ?></p>
            <p><strong>Clinic:</strong> <?= htmlspecialchars($doctor['clinic_name']) ?></p>
            <p><strong>Experience:</strong> <?= htmlspecialchars($doctor['experience']) ?> years</p>
            <p><strong>About:</strong> <?= htmlspecialchars($doctor['bio']) ?></p>
        </div>

        <h3>Book an Appointment</h3>
        <form action="process_booking.php" method="POST">
            <input type="hidden" name="doctor_id" value="<?= htmlspecialchars($doctor_id) ?>">
            <input type="hidden" name="patient_id" value="<?= htmlspecialchars($patient_id) ?>">

            <label for="appointment_date">Choose a Date:</label>
            <select name="appointment_date" id="appointment_date" required onchange="updateTimeOptions()">
                <option value="">-- Select a date --</option>
                <?php foreach ($schedule_data as $schedule): ?>
                    <?php 
                        $available_dates = getUpcomingDates($schedule['day_of_week']);
                        foreach ($available_dates as $date):
                    ?>
                        <option value="<?= $date ?>" data-start="<?= $schedule['start_time'] ?>" data-end="<?= $schedule['end_time'] ?>">
                            <?= date("l, F jS, Y", strtotime($date)) ?> (<?= $schedule['start_time'] ?> - <?= $schedule['end_time'] ?>)
                        </option>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </select>

            <label for="appointment_time">Choose a Time:</label>
            <input type="time" name="appointment_time" id="appointment_time" required>

            <label for="patient_name">Your Name:</label>
            <input type="text" name="patient_name" required>

            <label for="patient_email">Your Email:</label>
            <input type="email" name="patient_email" required>

            <button type="submit">Confirm Appointment</button>
        </form>
        <a href="patient-dashboard.php" class="back-button">← Back to dashboard</a>
    
 
 </div>
    <script>
        function updateTimeOptions() {
            let selectedDate = document.getElementById('appointment_date');
            let selectedOption = selectedDate.options[selectedDate.selectedIndex];
            let startTime = selectedOption.getAttribute('data-start');
            let endTime = selectedOption.getAttribute('data-end');
            
            let timeInput = document.getElementById('appointment_time');
            timeInput.min = startTime;
            timeInput.max = endTime;
        }
    </script>
</body>
</html>
