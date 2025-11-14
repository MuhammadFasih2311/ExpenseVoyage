<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}
require_once "connect.php";
require_once "inc/admin-header.php";
require_once "inc/admin-sidebar.php";
?>

<main class="col-12 col-md-12 col-lg-10 p-4">
  <h2 class="fw-bold mb-4 text-gradient" data-aos="fade-right">
    <i class="bi bi-house-door me-2"></i> Admin Home
  </h2>
<div class="card border-0 shadow-lg p-5 rounded-4 bg-light position-relative overflow-hidden" data-aos="fade-up">

  <!-- Decorative background gradient -->
  <div class="position-absolute top-0 start-0 w-100 h-100" 
       style="background: linear-gradient(135deg, rgba(0,123,255,0.08), rgba(255,193,7,0.08)); 
              z-index:0; border-radius: 1rem;">
  </div>

  <div class="position-relative" style="z-index:1;">
    <h4 class="fw-bold mb-3 text-dark">
      👋 Welcome, <span class="text-primary"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
    </h4>

    <p class="text-secondary mb-4">
      You are logged into the <strong class="text-dark">ExpenseVoyage Admin Panel</strong>.  
      Manage <span class="text-primary fw-semibold">users, trips, expenses, blogs</span>, and messages easily  
      using the sidebar navigation.
    </p>

    <div class="d-flex flex-wrap gap-3 mt-3">
      <a href="dashboard.php" class="btn btn-grad px-4 py-2 rounded-pill shadow-sm">
        <i class="bi bi-speedometer2 me-1"></i> Go to Dashboard
      </a>
      <a href="logout.php" class="btn btn-outline-danger px-4 py-2 rounded-pill">
        <i class="bi bi-box-arrow-right me-1"></i> Logout
      </a>
    </div>
  </div>
</div>

</main>

<?php require_once "inc/admin-footer.php"; ?>
