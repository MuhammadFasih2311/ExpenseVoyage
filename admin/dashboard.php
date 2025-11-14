<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: admin-login.php");
  exit;
}
require_once "connect.php";
require_once "inc/admin-header.php";
require_once "inc/admin-sidebar.php";

// Counts from DB
$total_users = (int)($conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'] ?? 0);
$total_trips = (int)($conn->query("SELECT COUNT(*) c FROM trips")->fetch_assoc()['c'] ?? 0);
$total_expense_trips = (int)(
  $conn->query("SELECT COUNT(DISTINCT trip_id) AS c FROM expenses")->fetch_assoc()['c'] ?? 0
);
$total_bookings = (int)($conn->query("SELECT COUNT(*) c FROM bookings")->fetch_assoc()['c'] ?? 0);
$total_paid = (int)($conn->query("SELECT COUNT(*) c FROM bookings WHERE payment_status='paid'")->fetch_assoc()['c'] ?? 0);
$total_unpaid = (int)($conn->query("SELECT COUNT(*) c FROM bookings WHERE payment_status='unpaid'")->fetch_assoc()['c'] ?? 0);
$total_msgs = (int)($conn->query("SELECT COUNT(*) c FROM contact_messages")->fetch_assoc()['c'] ?? 0);
$total_blogs = (int)($conn->query("SELECT COUNT(*) c FROM blogs")->fetch_assoc()['c'] ?? 0);
?>

<main class="col-12 col-md-12 col-lg-10 p-4">
  <h2 class="fw-bold mb-4 text-gradient" data-aos="fade-right">
    <i class="bi bi-speedometer2 me-2"></i> Dashboard
  </h2>

  <div class="row g-4">

    <!-- Users -->
    <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3">
      <a href="users.php" class="text-decoration-none">
        <div class="card card-kpi h-100" data-aos="zoom-in">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="kpi-icon bg-primary"><i class="bi bi-people"></i></div>
              <h6 class="mt-2">Users</h6>
            </div>
            <span><?= $total_users ?></span>
          </div>
        </div>
      </a>
    </div>

    <!-- Trips -->
    <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3">
      <a href="admin-trips.php" class="text-decoration-none">
        <div class="card card-kpi h-100" data-aos="zoom-in" data-aos-delay="50">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="kpi-icon bg-success"><i class="bi bi-airplane"></i></div>
              <h6 class="mt-2">Trips</h6>
            </div>
            <span><?= $total_trips ?></span>
          </div>
        </div>
      </a>
    </div>

    <!-- Expense Templates -->
<div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3">
  <a href="admin-expenses.php" class="text-decoration-none">
    <div class="card card-kpi h-100" data-aos="zoom-in" data-aos-delay="100">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="kpi-icon bg-warning"><i class="bi bi-wallet2"></i></div>
          <h6 class="mt-2">Users Expenses</h6>
        </div>
        <span><?= $total_expense_trips ?></span>
      </div>
    </div>
  </a>
</div>

 <!-- Bookings -->
<div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3">
  <a href="admin-bookings.php" class="text-decoration-none">
    <div class="card card-kpi h-100" data-aos="zoom-in" data-aos-delay="120">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="kpi-icon bg-purple"><i class="bi bi-journal-bookmark"></i></div>
          <h6 class="mt-2">Bookings</h6>
          <small class="text-success">Paid: <?= $total_paid ?></small> |
          <small class="text-danger">Unpaid: <?= $total_unpaid ?></small>
        </div>
        <span><?= $total_bookings ?></span>
      </div>
    </div>
  </a>
</div>

    <!-- Blogs -->
    <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3">
      <a href="admin-blogs.php" class="text-decoration-none">
        <div class="card card-kpi h-100" data-aos="zoom-in" data-aos-delay="150">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="kpi-icon bg-info"><i class="bi bi-journal-text"></i></div>
              <h6 class="mt-2">Blogs</h6>
            </div>
            <span><?= $total_blogs ?></span>
          </div>
        </div>
      </a>
    </div>

    <!-- Messages -->
    <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3">
      <a href="messages.php" class="text-decoration-none">
        <div class="card card-kpi h-100" data-aos="zoom-in" data-aos-delay="200">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="kpi-icon bg-danger"><i class="bi bi-envelope"></i></div>
              <h6 class="mt-2">Messages</h6>
            </div>
            <span><?= $total_msgs ?></span>
          </div>
        </div>
      </a>
    </div>

        <!-- Reports -->
<div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3">
  <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#reportsModal">
    <div class="card card-kpi h-100" data-aos="zoom-in" data-aos-delay="250">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="kpi-icon bg-secondary"><i class="bi bi-graph-up"></i></div>
          <h6 class="mt-2">Reports</h6>
        </div>
        <span><i class="bi bi-chevron-right"></i></span>
      </div>
    </div>
  </a>
</div>

  </div>
</main>

<?php require_once "inc/admin-footer.php"; ?>
