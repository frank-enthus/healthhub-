<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthHub - Your Central Hub for Health</title>
    <link rel="stylesheet" href="styles/styles.css">
     <link rel="stylesheet" href="styles/search.css">

    <script src="script/search.js" defer></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


</head>
<body>
    <!-- Header Section -->
    <header>
    <div class="container">
        <div class="logo">
            <img src="../healthhub project/images/hh.jpg" alt="HealthHub Logo" />
        </div>
        <nav>
            <ul class="nav-links">
                <li><a href="#">Home</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="#login" id="loginBtn" class="btn">Login/Sign Up</a></li>
            </ul>
            <div class="hamburger-menu">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </div>
</header>
<div id="userTypeModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Are you a Doctor or a Patient?</h2>
        <div class="modal-buttons">
            <button class="modal-btn doctor-btn">Doctor</button>
            <button class="modal-btn patient-btn">Patient</button>
        </div>
    </div>
</div>

<script src="script/modal.js"></script>
<script src="script/js.js"></script> <!-- Link to the external JavaScript -->
    <!-- Hero Section -->
   <section class="hero">
    <div class="container">
        <h1>Your Central Hub for Health Information & Services</h1>
        <p>Connect with the right doctors and healthcare facilities effortlessly.</p>
        
        <div class="cta-buttons">
            <button id="findDoctorBtn" class="btn primary">Find a Doctor</button>
            <button id="locateFacilityBtn" class="btn secondary">Locate a Facility</button>
        </div>

        <!-- Search Bar (Hidden Initially) -->
        <div id="facilitySearchContainer" class="search-container">
            <input type="text" id="searchInput" placeholder="Search by Name, Location, or Specialization">
            <div id="resultsDropdown" class="dropdown-content"></div>
        </div>
    </div>
</section>

    <!-- About Section -->
   <section id="about" class="about">
    <div class="container">
        <div class="about-content">
            <div class="about-text">
                <h2>ABOUT HEALTHHUB</h2>
                <p>HealthHub is designed to make healthcare accessible, efficient, and interconnected. Our platform helps you find the best care near you, book appointments, and stay informed about healthcare services.</p>
            </div>
            <div class="about-image">
                <img src="../healthhub project/images/about.jpg" alt="About HealthHub" />
            </div>
        </div>
    </div>
</section>

    <!-- Services Section -->
 <section id="services" class="services">
    <div class="container">
        <h2>OUR SERVICES</h2>
        <div class="services-grid">
            <div class="service-item">
                <img src="../healthhub project/images/find-doctor.jpg" alt="Find a Doctor">
                <h3>Find a Doctor</h3>
                <p>Connect with the best healthcare professionals near you.</p>
            </div>
            <div class="service-item">
                <img src="../healthhub project/images/book-appointment.jpg" alt="Book Appointments">
                <h3>Book Appointments</h3>
                <p>Easily schedule appointments with your preferred doctors.</p>
            </div>
            <div class="service-item">
                <img src="../healthhub project/images/emergency-care.jpg" alt="Emergency Care">
                <h3>Emergency Care</h3>
                <p>Quick access to emergency medical services.</p>
            </div>
            <div class="service-item">
                <img src="../healthhub project/images/locate-facility.webp" alt="Locate Facilities">
                <h3>Locate Facilities</h3>
                <p>Find nearby clinics and hospitals with ease.</p>
            </div>
        </div>
    </div>
</section>

    <!-- Testimonials Section -->
 <section class="testimonials">
    <div class="container">
        <h2>WHAT OUR USERS SAY</h2>
        <div class="testimonial-vertical">
            <div class="testimonial-item">
                <img src="../healthhub project/images/user1.jpg" alt="User 1">
                <div>
                    <p>"HealthHub made it so easy to find the right specialist near me. Highly recommended!"</p>
                    <h4>John Doe</h4>
                </div>
            </div>
            <div class="testimonial-item">
                <img src="../healthhub project/images/user2.jpg" alt="User 2">
                <div>
                    <p>"I love how seamless it is to book appointments through HealthHub. Great service!"</p>
                    <h4>Jane Smith</h4>
                </div>
            </div>
            <div class="testimonial-item">
                <img src="../healthhub project/images/user3.jpg" alt="User 3">
                <div>
                    <p>"HealthHub's emergency care feature saved us during a critical situation. Thank you!"</p>
                    <h4>Emily Johnson</h4>
                </div>
            </div>
            <div class="testimonial-item">
                <img src="../healthhub project/images/user4.jpg" alt="User 4">
                <div>
                    <p>"Using HealthHub has been a game changer for managing my family's healthcare needs."</p>
                    <h4>Michael Brown</h4>
                </div>
            </div>
            <div class="testimonial-item">
                <img src="../healthhub project/images/user5.jpg" alt="User 5">
                <div>
                    <p>"HealthHub's user-friendly interface makes finding healthcare services a breeze!"</p>
                    <h4>Sarah Lee</h4>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="contact" class="contact">
    <div class="container">
        <h2>CONTACT US</h2>
        <p>Have questions? Reach out to us!</p>

        <div class="contact-wrapper">
            <!-- Left Side: Contact Details -->
            <div class="contact-info">
                <h3>Get in Touch</h3>
                <p>We're here to assist you. Reach us via the details below:</p>
                <ul>
                    <li><i class="fas fa-envelope"></i> Email: support@healthhub.com</li>
                    <li><i class="fas fa-phone"></i> Phone: +123-456-7890</li>
                    <li><i class="fas fa-map-marker-alt"></i> Address: 123 Health Street, City, Country</li>
                </ul>
            </div>

            <!-- Right Side: Contact Form -->
            <div class="contact-form">
                <h3>Send Us a Message</h3>
                <form action="contact_process.php" method="POST">
                    <input type="text" name="name" placeholder="Your Name" required>
                    <input type="email" name="email" placeholder="Your Email" required>
                    <input type="text" name="subject" placeholder="Subject" required>
                    <textarea name="message" placeholder="Your Message" required></textarea>
                    <button type="submit" class="btn primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>






    <!-- Footer Section -->
 <footer>
    <div class="footer-container">
        <div class="footer-about">
            <h3>About HealthHub</h3>
            <p>Your central hub for all healthcare-related services. Connect, book, and manage your health effortlessly.</p>
        </div>
        <div class="footer-links">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </div>
        <div class="footer-social">
            <h3>Follow Us</h3>
            <ul class="social-icons">
                <li><a href="https://facebook.com" target="_blank"><i class="fab fa-facebook"></i></a></li>
                <li><a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a></li>
                <li><a href="https://linkedin.com" target="_blank"><i class="fab fa-linkedin"></i></a></li>
                <li><a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2025 HealthHub. All rights reserved.</p>
    </div>
</footer>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const sections = document.querySelectorAll("section");

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("fade-in");
        } else {
          entry.target.classList.remove("fade-in"); // Reapply animation when scrolling back up
        }
      });
    },
    { threshold: 0.6 } // Adjust visibility percentage before triggering animation
  );

  sections.forEach((section) => {
    observer.observe(section);
  });
});


</script>

</body>
</html>
