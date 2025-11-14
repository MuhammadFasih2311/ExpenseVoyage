<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<?php
include 'connect.php';
$alert = "";

// Form Submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);

    $sql = "INSERT INTO contact_messages (name, email, subject, message)
            VALUES ('$name', '$email', '$subject', '$message')";

    if ($conn->query($sql) === TRUE) {
        $alert = "<div class='alert alert-success text-center'>Your message has been sent successfully!</div>";
    } else {
        $alert = "<div class='alert alert-danger text-center'>Something went wrong. Please try again.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Get in touch with Expense Voyage – we’re here to help with your travel bookings, expense queries, and trip planning support. Contact us today.">
<title>Contact Us - Expense Voyage</title>
<link rel="icon" href="images/logo.png" type="image/png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<style>
  html, body {
  overflow-x: hidden;
}
/* Parallax Background */
.contact-hero {
    background-image: url('images/contact.jpg');
    background-attachment: fixed;
    background-position: center;
    background-size: cover;
    height: 350px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}
.contact-hero::before {
    content: "";
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
}
.contact-hero h1 {
    color: #fff;
    z-index: 1;
    font-weight: bold;
}

/* Form Style */
.contact-form {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.contact-form .btn {
    background: linear-gradient(45deg, #007bff, #ff8c42);
    color: #fff;
    border: none;
    transition: color 0.3s ease;
}
.contact-form .btn:hover {
    opacity: 0.9;
    background: linear-gradient(45deg, #ff8c42, #007bff);
    color: #ffffffff;
}
</style>
</head>
<body>

<?php include 'header.php'; ?>

<!-- Parallax Hero Section -->
<section class="contact-hero text-center text-white">
  <div class="hero-content position-relative" data-aos="zoom-in">
    <h1 class="fw-bold">Contact Us</h1>
    <p class="lead">We’d love to hear from you — let’s plan your journey together</p>
  </div>
</section>

<!-- Contact Form -->
<section class="py-5">
  <div class="container">
    <?= $alert; ?>
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="contact-form p-4 p-md-5 shadow-lg rounded-3 bg-white" data-aos="fade-up" data-aos-delay="300" style="border-top: 5px solid #007bff;">
          <h3 class="mb-4 text-center section-title fw-bold" data-aos="zoom-in" data-aos-delay="400">Get in Touch</h3>
          <p class="text-muted text-center mb-4" data-aos="fade-up" data-aos-delay="400">We’d love to hear from you! Fill out the form and we’ll get back to you shortly.</p>
          
          <form method="POST">
            <!-- Name -->
            <div class="mb-3" data-aos="fade-right" data-aos-delay="400">
              <label class="form-label fw-semibold">Name</label>
              <input type="text" name="name" class="form-control form-control-lg rounded-pill shadow-sm" placeholder="Enter your name" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')" required maxlength="30" minlength="3">
            </div>

            <!-- Email -->
            <div class="mb-3" data-aos="fade-left" data-aos-delay="450">
              <label class="form-label fw-semibold">Email</label>
              <input type="email" name="email" class="form-control form-control-lg rounded-pill shadow-sm" placeholder="Enter your email" required maxlength="30" minlength="4">
            </div>

            <!-- Subject -->
            <div class="mb-3" data-aos="fade-right" data-aos-delay="500">
              <label class="form-label fw-semibold">Subject</label>
              <input type="text" name="subject" class="form-control form-control-lg rounded-pill shadow-sm" placeholder="Enter subject" required maxlength="30" minlength="4">
            </div>

            <!-- Message -->
            <div class="mb-4" data-aos="fade-left" data-aos-delay="550">
              <label class="form-label fw-semibold">Message</label>
              <textarea name="message" class="form-control shadow-sm rounded-3" rows="5" placeholder="Write your message here..." required maxlength="250" minlength="5"></textarea>
            </div>

            <!-- Submit -->
            <div class="text-center" data-aos="zoom-in" data-aos-delay="590">
              <button type="submit" class="btn px-5 py-2 text-white">
                Send Message
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Google Map Section -->
<section class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold section-title" data-aos="fade-up" data-aos-delay="200">Location</h2>
      <p class="text-muted" data-aos="fade-up" data-aos-delay="300">Discover amazing destinations around the world</p>
    </div>
    <div class="map-container shadow rounded overflow-hidden" style="height: 400px;" data-aos="zoom-in-up" data-aos-delay="400">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3620.642907765988!2d67.05033339851039!3d24.841882644267244!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMjTCsDUwJzI1LjgiTiA2N8KwMDMnMDcuMCJF!5e0!3m2!1sen!2s!4v1755175306820!5m2!1sen!2s"
        width="100%"
        height="100%"
        style="border:0;"
        allowfullscreen=""
        loading="lazy">
      </iframe>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
 AOS.init({
    duration: 700, // animation ka time (ms) slow aur smooth ke liye
    easing: 'ease-in-out', // smooth easing
    once: false, // baar-baar animation chalay
  });
</script>
<script>
// Smooth scroll to top on success alert
document.addEventListener("DOMContentLoaded", function () {
    let alertBox = document.querySelector(".alert-success");
    if (alertBox) {
        window.scrollTo({ top: 0, behavior: 'smooth' });

        // Animation for alert
        alertBox.style.opacity = 0;
        alertBox.style.transition = "opacity 0.8s ease-in-out";
        setTimeout(() => {
            alertBox.style.opacity = 1;
        }, 200);

        // Auto hide after 4s
        setTimeout(() => {
            alertBox.style.opacity = 0;
        }, 4000);
    }
});
</script>

</body>
</html>
