<?php
session_start();
include('db_connect.php');

// Ensure only the admin can access
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

// Fetch data counts
$totalDoctors = $conn->query("SELECT COUNT(*) AS count FROM Doctors")->fetch_assoc()['count'];
$totalPatients = $conn->query("SELECT COUNT(*) AS count FROM Patients")->fetch_assoc()['count'];
$totalHospitals = $conn->query("SELECT COUNT(*) AS count FROM Hospitals")->fetch_assoc()['count'];

$doctors = $conn->query("SELECT u.id, u.full_name, u.email, u.profile_picture, 
                        d.registration_number, d.specialization, d.clinic_name, d.clinic_location 
                        FROM Users u 
                        JOIN Doctors d ON u.id = d.user_id 
                        WHERE u.user_type = 'doctor' 
                        ORDER BY u.full_name ASC");

// Fetch patients from the database
$patients = $conn->query("SELECT p.id, p.full_name, p.email, p.profile_picture, 
                                 p.phone, p.gender, p.weight, p.age 
                          FROM Patients p 
                          ORDER BY p.full_name ASC");




// Fetch hospitals
$hospitals = $conn->query("SELECT * FROM Hospitals");


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles/admin.css">
   
</head>
<body>
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="#stats-container">Dashboard</a></li>
            <li><a href="#doctor-list">Manage Doctors</a></li>
            <li><a href="#patient-list">Manage Patients</a></li>
            <li><a href="#hospital-directory">Manage Hospitals</a></li>
            <li><a href="admin-messages.php">Messages</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1>Welcome, Admin</h1>
        <div id="stats-container" class="stats-container">
            <div class="stat-box"><h3>Total Doctors </h3><p><?php echo $totalDoctors; ?></p>
            <a href="export_doctors.php" class="export-btn">Export Doctors List</a>
            </div>
            <div class="stat-box"><h3>Total Patients</h3><p><?php echo $totalPatients; ?></p>
             <a href="export_patients.php" class="export-btn">Export Patient List</a></div>
            <div class="stat-box"><h3>Total Hospitals</h3><p><?php echo $totalHospitals; ?></p>
             <a href="export_hospitals.php" class="export-btn">Export hospital List</a></div>
        </div>
        <div class="management-container">
    <!-- Manage Hospitals -->
    <div class="management-section">
        <h2>Manage Hospitals</h2>
        <form action="add_hospital.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Hospital Name" required>
            <input type="text" id="hospital-location" name="location" placeholder="Location" required onblur="getCoordinates()">
            <input type="text" name="contact" placeholder="Contact Number" required>
            <input type="text" name="specialties" placeholder="Specialties">
            <input type="text" name="services" placeholder="Services">
            <input type="text" name="operating_hours" placeholder="Operating Hours">
            <input type="url" name="website" placeholder="Hospital Website">
            <textarea name="description" placeholder="Short Description"></textarea>
            <input type="text" id="latitude" name="latitude" placeholder="Latitude" required>
            <input type="text" id="longitude" name="longitude" placeholder="Longitude" required>
            <input type="file" name="image" accept="image/*" required>
            <button type="submit">Add Hospital</button>
        </form>
    </div>

    <!-- Manage Doctors -->
    <div class="management-section">
        <h2>Manage Doctors</h2>
        <form action="add_doctor.php" method="POST" enctype="multipart/form-data">
    <input type="text" name="full_name" placeholder="Doctor Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="phone" placeholder="Phone Number" required>
    <input type="text" name="registration_number" placeholder="Registration Number" required>
    <input type="text" name="specialization" placeholder="Specialization" required>
    <input type="text" name="clinic_name" placeholder="Clinic Name">
    <input type="text" name="clinic_location" placeholder="Clinic Location">

    <label>Profile Picture:</label>
    <input type="file" name="profile_picture" accept="image/*">

    <button type="submit">Add Doctor</button>
</form>

    </div>

    <!-- Manage Patients -->
    <div class="management-section">
    <h2>Manage Patients</h2>
    <form action="add_patient.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="full_name" placeholder="Patient Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone Number">
        <input type="text" name="gender" placeholder="Gender" required>
        <input type="number" name="age" placeholder="Age" required>
        <input type="number" name="weight" placeholder="Weight (kg)">
        <input type="text" name="languages" placeholder="Languages Spoken" required>
        <input type="file" name="profile_picture" accept="image/*">
        <button type="submit">Add Patient</button>
    </form>
</div>

</div>
        
        <div id="hospital-directory"class="hospital-list" border="3">
       
            <h2>Hospital Directory</h2>
            <input type="text" id="hospitalSearch" placeholder="Search Hospitals..." onkeyup="searchHospitals()">
            <table>
         
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Contact</th>
                        <th>Specialties</th>
                        <th>Services</th>
                        <th>Operating Hours</th>
                        <th>Website</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($hospital = $hospitals->fetch_assoc()) { ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($hospital['image']); ?>" alt="Hospital Image" class="hospital-img"></td>
                            <td><?php echo htmlspecialchars($hospital['name']); ?></td>
                            <td><?php echo htmlspecialchars($hospital['location']); ?></td>
                            <td><?php echo htmlspecialchars($hospital['contact']); ?></td>
                            <td><?php echo htmlspecialchars($hospital['specialties']); ?></td>
                            <td><?php echo htmlspecialchars($hospital['services']); ?></td>
                            <td><?php echo htmlspecialchars($hospital['operating_hours']); ?></td>
                            <td><a href="<?php echo htmlspecialchars($hospital['website']); ?>" target="_blank">Visit</a></td>
                            <td>
                                <a href="edit_hospital.php?id=<?php echo $hospital['id']; ?>" class="edit-btn">Edit</a>
                                <form action="delete_hospital.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="hospital_id" value="<?php echo $hospital['id']; ?>">
                                    <button type="submit" class="delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>  
        </div>
        
        <div id="doctor-list" class="doctor-list"  border="3">

       
       <h2>Doctor list</h2>

       <table>
       <input type="text" id="doctorSearch" placeholder="Search Doctors..." onkeyup="searchDoctors()">

            <thead>
                <tr>
                    <th>Profile Picture</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Registration Number</th>
                    <th>Specialization</th>
                    <th>Clinic Name</th>
                    <th>Clinic Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($doctor = $doctors->fetch_assoc()) { ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($doctor['profile_picture']); ?>" alt="Doctor Image" class="doctor-img"></td>
                        <td><?php echo htmlspecialchars($doctor['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['email']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['registration_number']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['specialization']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['clinic_name']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['clinic_location']); ?></td>
                        <td>
                            <a href="doctor_details.php?id=<?php echo $doctor['id']; ?>" class="view-btn">View Profile</a>
                            <form action="delete_doctor.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this doctor?');">
                                <input type="hidden" name="doctor_id" value="<?php echo $doctor['id']; ?>">
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
   </div>


   <div id="patient-list" class="patient-list" border="3">    
    <h2>Patient List</h2>
    <table>
    <input type="text" id="patientSearch" placeholder="Search Patient..." onkeyup="searchPatient()">
        <thead>
            <tr>
                <th>Profile Picture</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Gender</th>
                <th>Weight (kg)</th>
                <th>Age</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($patient = $patients->fetch_assoc()) { ?>
                <tr>
                    <td>
                        <img src="<?php echo htmlspecialchars($patient['profile_picture'] ?: 'default.jpg'); ?>" 
                             alt="Patient Image" class="patient-img" width="50">
                    </td>
                    <td><?php echo htmlspecialchars($patient['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($patient['email']); ?></td>
                    <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                    <td><?php echo htmlspecialchars($patient['gender']); ?></td>
                    <td><?php echo htmlspecialchars($patient['weight'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($patient['age'] ?? 'N/A'); ?></td>
                    <td>
                        
                        <form action="delete_patient.php" method="POST" style="display:inline;" 
                              onsubmit="return confirm('Are you sure you want to delete this patient?');">
                              <input type="hidden" name="patient_id" value="<?php echo $patient['id']; ?>">

                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
    </main>
    <script>
        function searchDoctors() {
    let input = document.getElementById('doctorSearch').value.toUpperCase();
    let table = document.querySelector('.doctor-list table');
    let rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        let name = rows[i].getElementsByTagName('td')[1].innerText;
        if (name.toUpperCase().indexOf(input) > -1) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}
</script>
<script>
        function searchPatient() {
    let input = document.getElementById('patientSearch').value.toUpperCase();
    let table = document.querySelector('.patient-list table');
    let rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        let name = rows[i].getElementsByTagName('td')[1].innerText;
        if (name.toUpperCase().indexOf(input) > -1) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}
</script>
<script>
        function searchHospitals() {
    let input = document.getElementById('hospitalSearch').value.toUpperCase();
    let table = document.querySelector('.hospital-list table');
    let rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        let name = rows[i].getElementsByTagName('td')[1].innerText;
        if (name.toUpperCase().indexOf(input) > -1) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}
</script>

    
</body>
</html>
