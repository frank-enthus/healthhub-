// Get Modal and Elements
const modal = document.getElementById('userTypeModal');
const loginBtn = document.getElementById('loginBtn');
const closeBtn = document.querySelector('.close-btn');
const doctorBtn = document.querySelector('.doctor-btn');
const patientBtn = document.querySelector('.patient-btn');

// Show Modal on Button Click
loginBtn.addEventListener('click', () => {
    // Show the modal
    modal.style.display = 'block';

    // Position the modal below the Login/Sign Up button
    const rect = loginBtn.getBoundingClientRect(); // Get the button's position
    const modalContent = modal.querySelector('.modal-content');
    modalContent.style.position = 'absolute';
    modalContent.style.top = `${rect.bottom + window.scrollY + 10}px`; // 10px below the button
    modalContent.style.left = `${rect.left + window.scrollX}px`; // Align with the button
});

// Hide Modal on Close Button Click
closeBtn.addEventListener('click', () => {
    modal.style.display = 'none'; // Hide the modal
});

// Hide Modal on Outside Click
window.addEventListener('click', (event) => {
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});

// Redirect on Doctor Button Click
doctorBtn.addEventListener('click', () => {
    window.location.href = 'doctor.php'; // Replace with the actual URL for doctors
});

// Redirect on Patient Button Click
patientBtn.addEventListener('click', () => {
    window.location.href = 'patient.php'; // Replace with the actual URL for patients
});
