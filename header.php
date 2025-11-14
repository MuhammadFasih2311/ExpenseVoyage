<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ExpenseVoyage</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- AOS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <link rel="stylesheet" href="style.css">

  <style>
    /* Navbar background */
.custom-navbar {
  background: linear-gradient(90deg, #0855ca, #ff7a00);
  padding: 15px 0;
}

/* Custom Hamburger */
.custom-toggler {
  border: none;
  background: #fff;
  padding: 8px 10px;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  transition: all 0.3s ease;
}
.custom-toggler:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(255,122,0,0.4);
}

.toggler-icon {
  display: block;
  width: 24px;
  height: 3px;
  margin: 4px auto;
  background-color: #ff7a00;
  border-radius: 2px;
  transition: all 0.3s ease-in-out;
}

/* Open state */
.custom-toggler.active .toggler-icon:nth-child(1) {
  transform: translateY(7px) rotate(45deg);
}
.custom-toggler.active .toggler-icon:nth-child(2) {
  opacity: 0;
}
.custom-toggler.active .toggler-icon:nth-child(3) {
  transform: translateY(-7px) rotate(-45deg);
}
.sign-btn {
  background: linear-gradient(90deg, #007bff, #00c6ff);
  color: white;
  border-radius: 15%;
  padding: 7px 12px;
  text-decoration: none;
}
.sign-btn:hover {
  opacity: 0.9;
  color: white;
}
/* Dropdown custom styling */
.custom-dropdown {
  background: linear-gradient(135deg, #0855ca, #ff7a00); /* blue to orange */
  border: none;
  border-radius: 12px;
  padding: 8px 0;
  box-shadow: 0 6px 18px rgba(0,0,0,0.2);
  animation: fadeIn 0.3s ease-in-out;
}

.custom-dropdown .dropdown-item {
  color: #fff;
  font-weight: 500;
  transition: all 0.3s ease;
  padding: 10px 16px;
  border-radius: 6px;
}

.custom-dropdown .dropdown-item:hover {
  background: rgba(255, 255, 255, 0.15);
  color: #ffc107; /* yellow text */
  transform: translateX(4px);
}

.custom-dropdown .dropdown-divider {
  border-color: rgba(37, 49, 223, 0.95);
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to   { opacity: 1; transform: translateY(0); }
}
  </style>
</head>
<body>
<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top custom-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php" data-aos="fade-right" data-aos-delay="350">
      <img src="images\logo.png" alt="" width="50px">
      <span style="color:#ff7a00">Expense</span><span style="color:#fff;">Voyage</span>
    </a>

    <!-- ✅ Custom Hamburger -->
     <button class="navbar-toggler custom-toggler" type="button" id="hamburgerBtn" data-aos="fade-down" data-aos-delay="350">
      <span class="toggler-icon"></span>
      <span class="toggler-icon"></span>
      <span class="toggler-icon"></span>
    </button>

   
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item" data-aos="fade-down" data-aos-delay="400"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item" data-aos="fade-down" data-aos-delay="450"><a class="nav-link" href="trips.php">Trips</a></li>
        <li class="nav-item" data-aos="fade-down" data-aos-delay="500"><a class="nav-link" href="expenses.php">Expenses</a></li>
        <li class="nav-item" data-aos="fade-down" data-aos-delay="550"><a class="nav-link" href="book-trip.php">Bookings</a></li>
        <li class="nav-item" data-aos="fade-down" data-aos-delay="600"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item" data-aos="fade-down" data-aos-delay="650"><a class="nav-link" href="blogs.php">Blogs</a></li>
        <li class="nav-item" data-aos="fade-down" data-aos-delay="700"><a class="nav-link" href="contact.php">Contact</a></li>

        <?php if(isset($_SESSION['user_id'])):?>
          <li class="nav-item dropdown" data-aos="fade-down" data-aos-delay="750">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
              <i class="fa-regular fa-circle-user me-2"></i>
              <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end custom-dropdown">
            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person-circle me-2"></i> Profile</a></li>
            <li><a class="dropdown-item" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
            <li><a class="dropdown-item" href="my-bookings.php"><i class="bi bi-journal-check me-2"></i> My Bookings</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
          </ul>
          </li>
        <?php else: ?>
          <li class="nav-item ms-2"><a class="btn btn-gradient text-white px-3" href="login.php" data-aos="fade-down" data-aos-delay="750">Login</a></li>
          <li class="nav-item ms-2 d-none d-lg-block" data-aos="fade-down" data-aos-delay="800"><a class="btn sign-btn text-light px-3" href="register.php">Sign Up</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- spacer -->
<div style="height:72px;"></div>

<!-- Scripts -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const hamburgerBtn = document.getElementById("hamburgerBtn");
  const navbarNav = document.getElementById("navbarNav");

  hamburgerBtn.addEventListener("click", function () {
    navbarNav.classList.toggle("show");
    this.classList.toggle("active");
  });
});

AOS.init({
    duration: 800, // animation ka time (ms) slow aur smooth ke liye
    easing: 'ease-in-out', // smooth easing
    once: false, // baar-baar animation chalay
  });
</script>
</body>
</html>
