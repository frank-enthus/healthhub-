// Select form and modal elements
const form = document.getElementById('signup-form');
const modal = document.getElementById('confirmation-modal');
const closeBtn = document.querySelector('.close');

// Handle form submission
form.addEventListener('submit', (e) => {
  e.preventDefault(); // Prevent page refresh

  // Display the modal
  modal.style.display = 'block';
});

// Close the modal when clicking on the close button
closeBtn.addEventListener('click', () => {
  modal.style.display = 'none';
});

// Close the modal when clicking outside of it
window.addEventListener('click', (e) => {
  if (e.target === modal) {
    modal.style.display = 'none';
  }
});
