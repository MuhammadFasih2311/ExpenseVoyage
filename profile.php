<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
include 'connect.php';
$user_id = $_SESSION['user_id'];

// User Data
$user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE id=$user_id"));

// Stats
$q1 = mysqli_query($conn,"SELECT COUNT(*) as c FROM bookings WHERE user_id=$user_id");
$total_bookings = mysqli_fetch_assoc($q1)['c'] ?? 0;

$q2 = mysqli_query($conn,"SELECT COUNT(*) as c FROM trips");
$total_trips = mysqli_fetch_assoc($q2)['c'] ?? 0;

$q3 = mysqli_query($conn,"SELECT SUM(amount) as s FROM expenses WHERE user_id=$user_id");
$total_expenses = mysqli_fetch_assoc($q3)['s'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Manage your personal details and account settings on Expense Voyage. Keep your profile updated for a smooth travel booking and expense tracking experience.">
<title>My Profile - Expense Voyage</title>
  <link rel="icon" href="images/logo.png" type="image/png">
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
  <style>
    html, body {
  overflow-x: hidden;
}
    body { background:#f8f9fa; }
    .profile-hero {
      background:linear-gradient(to right, #6a11cb, #2575fc);
      height:250px; position:relative; color:white; text-align:center;
      display:flex; justify-content:center; align-items:center;
    }
    .profile-card {
      margin-top:-80px;
      background:white; border-radius:20px; padding:30px;
      box-shadow:0 8px 25px rgba(0,0,0,0.1);
      text-align:center;
    }
    .stat-card {
      border-radius:16px; background:white;
      box-shadow:0 4px 15px rgba(0,0,0,0.1);
      padding:20px; transition:.3s;
    }
    .stat-card:hover { transform:translateY(-5px); }
  </style>
</head>
<body class="bg-light">
<?php include 'header.php'; ?>

<!-- Hero -->
<section class="profile-hero">
  <div>
    <h1 class="fw-bold" data-aos="fade-down"><?= htmlspecialchars($user['first_name']." ".$user['last_name']); ?></h1>
    <p data-aos="fade-down"><?= htmlspecialchars($user['email']); ?></p>
  </div>
</section>

<div class="container">
  <div class="profile-card" data-aos="zoom-in">

    <?php if(isset($_SESSION['success'])): ?>
  <div class="container mt-4">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= $_SESSION['success']; unset($_SESSION['success']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  </div>
<?php endif; ?>

    <h4 class="fw-bold mb-3">Personal Information</h4>
    <p><strong>Name:</strong> <?= htmlspecialchars($user['first_name']." ".$user['last_name']); ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
    <p><strong>Joined:</strong> <?= date("M d, Y", strtotime($user['created_at'])); ?></p>

    <div class="mt-4">
      <a href="edit-profile.php" class="btn btn-outline-primary me-2">
        <i class="bi bi-pencil"></i> Edit Profile
      </a>
      <a href="change-password.php" class="btn btn-outline-warning">
        <i class="bi bi-shield-lock"></i> Change Password
      </a>
    </div>
  </div>

  <div class="row mt-5 g-4">
    <div class="col-md-4" data-aos="fade-up">
      <div class="stat-card text-center">
        <i class="bi bi-journal-bookmark-fill text-primary fs-2 mb-2"></i>
        <h4><?= $total_bookings ?></h4>
        <small>Total Bookings</small>
      </div>
    </div>
    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
      <div class="stat-card text-center">
        <i class="bi bi-globe2 text-success fs-2 mb-2"></i>
        <h4><?= $total_trips ?></h4>
        <small>Total Trips</small>
      </div>
    </div>
    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
      <div class="stat-card text-center">
        <i class="bi bi-cash-stack text-warning fs-2 mb-2"></i>
        <h4>$<?= number_format($total_expenses) ?></h4>
        <small>Total Expenses</small>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>AOS.init({ duration:800, easing:"ease-in-out" ,once:false });

    // Auto-hide success alert after 4 seconds
setTimeout(() => {
  let alert = document.querySelector(".alert-success");
  if(alert){
    let bsAlert = new bootstrap.Alert(alert);
    bsAlert.close();
  }
}, 4000);

</script>
</body>
</html>
