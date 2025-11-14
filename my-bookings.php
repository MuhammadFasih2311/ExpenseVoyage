<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'connect.php';

$user_id = $_SESSION['user_id'];

/* ---------- AJAX actions (no reload) ---------- */
// Cancel booking (POST)
if (isset($_POST['cancel_id']) && is_numeric($_POST['cancel_id'])) {
    $cancel_id = intval($_POST['cancel_id']);
    $q = "UPDATE bookings SET status='cancelled' WHERE id=$cancel_id AND user_id=$user_id";
    if (mysqli_query($conn, $q)) {
        echo "cancelled";
    } else {
        echo "error";
    }
    exit();
}
// Delete booking (POST) – keep same behavior but without reload
if (isset($_POST['delete_id']) && is_numeric($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $q = "DELETE FROM bookings WHERE id=$delete_id AND user_id=$user_id";
    if (mysqli_query($conn, $q)) {
        echo "deleted";
    } else {
        echo "error";
    }
    exit();
}

/* ---------- Filters & Pagination ---------- */
$status_filter = isset($_GET['status']) ? $_GET['status'] : "all";
$allowed_status = ["pending","confirmed","cancelled"];
$filter_sql = "";
if (in_array($status_filter, $allowed_status)) {
    $filter_sql = "AND b.status = '".mysqli_real_escape_string($conn, $status_filter)."'";
}

$per_page = 8;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && intval($_GET['page']) > 0 ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $per_page;

// total count (for pagination)
$count_sql = "SELECT COUNT(*) AS total
              FROM bookings b
              WHERE b.user_id = $user_id $filter_sql";
$count_res = mysqli_query($conn, $count_sql);
$total_rows = ($count_res && mysqli_num_rows($count_res)) ? intval(mysqli_fetch_assoc($count_res)['total']) : 0;
$total_pages = max(1, (int)ceil($total_rows / $per_page));

// main data
$sql = "SELECT b.*, t.trip_name, t.destination, t.start_date, t.end_date, t.budget, t.image 
        FROM bookings b
        JOIN trips t ON b.trip_id = t.id
        WHERE b.user_id = $user_id $filter_sql
        ORDER BY b.booking_date DESC
        LIMIT $per_page OFFSET $offset";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="View and manage all your trip bookings with Expense Voyage. Keep track of your upcoming journeys and travel plans in one place.">
<title>My Bookings - Expense Voyage</title>
  <link rel="icon" href="images/logo.png" type="image/png">
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    html, body {
  overflow-x: hidden;
}
    body { background: #f8f9fa; }
    .booking-card {
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      transition: transform 0.2s, opacity 0.3s;
      position: relative;
    }
    .booking-card:hover { transform: translateY(-4px); }
    .status-badge {
      font-size: 0.85rem; padding: 6px 10px; border-radius: 20px;
    }
    .delete-btn {
      position: absolute; top: 10px; right: 10px;
      background: rgba(220,53,69,0.9); color:#fff; border:none; border-radius:50%;
      width:32px; height:32px; display:flex; align-items:center; justify-content:center;
      cursor:pointer; transition: background 0.2s; z-index:10;
    }
    .delete-btn:hover { background: rgba(200,35,51,1); }
    /* pretty filter */
    .filter-wrap { background:#fff; border-radius:12px; padding:8px 12px; box-shadow:0 2px 10px rgba(0,0,0,0.06); }
    .nav-pills .nav-link { border-radius:20px; }
  </style>
</head>
<body class="bg-light"> 

<?php include 'header.php'; ?>

<section class="page-hero d-flex flex-column align-items-center justify-content-center text-center position-relative"
  style="background-image:url('images/my-bookings.jpg'); background-attachment:fixed; background-size:cover; background-position:center; height:300px;">
  <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.5);"></div>
  <div class="container position-relative text-white" data-aos="zoom-in">
    <h1 class="fw-bold">My Bookings</h1>
    <p class="lead">Track your upcoming adventures</p>
  </div>
</section>

<!-- Filter -->
<div class="container mt-4">
  <div class="d-flex justify-content-end">
    <div class="filter-wrap">
      <ul class="nav nav-pills">
        <?php
          function activeTab($cur, $tab){ return $cur===$tab ? 'active' : ''; }
          $sf = $status_filter;
        ?>
        <li class="nav-item" data-aos="fade-left" data-aos-delay="100"><a class="nav-link <?= ($sf==='all')?'active':''; ?>" href="?status=all">All</a></li>
        <li class="nav-item" data-aos="fade-left" data-aos-delay="200"><a class="nav-link <?= ($sf==='pending')?'active':''; ?>" href="?status=pending">Pending</a></li>
        <li class="nav-item" data-aos="fade-left" data-aos-delay="300"><a class="nav-link <?= ($sf==='confirmed')?'active':''; ?>" href="?status=confirmed">Confirmed</a></li>
        <li class="nav-item" data-aos="fade-left" data-aos-delay="400"><a class="nav-link <?= ($sf==='cancelled')?'active':''; ?>" href="?status=cancelled">Cancelled</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="container py-5">
  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      🎉 Booking confirmed successfully!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if ($result && mysqli_num_rows($result) > 0): ?>
    <div class="row g-4">
  <?php 
    $i = 0; // counter for delay
    while ($row = mysqli_fetch_assoc($result)): 
      $i++;
      // alternate delay: odd => 100, even => 200
      $delay = ($i % 2 == 1) ? 100 : 200;
  ?>
    <div class="col-md-6" data-aos="fade-up" data-aos-delay="<?= $delay; ?>" id="booking-card-<?= $row['id']; ?>">
      <div class="card booking-card h-100">
        <!-- ❌ Delete button (only if already cancelled) -->
        <?php if ($row['status'] == 'cancelled'): ?>
          <button class="delete-btn" onclick="deleteBooking(<?= $row['id']; ?>)">
            <i class="bi bi-x"></i>
          </button>
        <?php endif; ?>

        <?php if (!empty($row['image'])): ?>
          <img src="<?= htmlspecialchars($row['image']); ?>"
               alt="<?= htmlspecialchars($row['trip_name']); ?>"
               style="height:200px; width:100%; object-fit:cover;">
        <?php endif; ?>

            <div class="card-body">
              <h4 class="fw-bold"><?= htmlspecialchars($row['trip_name']); ?></h4>
              <p class="text-muted mb-1"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($row['destination']); ?></p>
              <p class="small text-muted mb-1">
                <?= date("M d, Y", strtotime($row['start_date'])); ?> - <?= date("M d, Y", strtotime($row['end_date'])); ?>
              </p>
              <p class="mb-1"><strong>Budget:</strong> $<?= number_format($row['budget']); ?></p>
              <p class="mb-1"><strong>Persons:</strong> <?= (int)$row['num_persons']; ?></p>
              <?php if (!empty($row['transaction_id'])): ?>
              <p class="mb-3"><strong>Transaction ID:</strong> <?= htmlspecialchars($row['transaction_id']); ?></p>
              <?php endif; ?>

              <!-- Status badges -->
              <span data-badge="status" class="status-badge bg-<?php 
                if ($row['status'] == 'confirmed') echo 'success';
                elseif ($row['status'] == 'cancelled') echo 'danger';
                else echo 'secondary';
              ?> text-white"><?= ucfirst($row['status']); ?></span>

              <span class="status-badge bg-<?php 
                if ($row['payment_status'] == 'paid') echo 'success';
                elseif ($row['payment_status'] == 'refunded') echo 'warning';
                else echo 'dark';
              ?> text-white"><?= ucfirst($row['payment_status']); ?></span>
            </div>

            <div class="card-footer text-end bg-white">
              <?php if ($row['status'] == 'pending'): ?>
                <button class="btn btn-sm btn-outline-danger" onclick="cancelBooking(<?= $row['id']; ?>)">
                  Cancel
                </button>
              <?php endif; ?>
              <a href="trip-details.php?id=<?= $row['trip_id']; ?>" class="btn btn-sm btn-gradient">View Trip</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
      <nav class="mt-4" data-aos="fade-up" data-aos-delay="100">
        <ul class="pagination justify-content-center">
          <?php
            $base = '?status=' . urlencode($status_filter) . '&page=';
            $prev_page = max(1, $page - 1);
            $next_page = min($total_pages, $page + 1);
          ?>
          <li class="page-item <?= ($page<=1)?'disabled':''; ?>">
            <a class="page-link" href="<?= $base.$prev_page; ?>" tabindex="-1">Previous</a>
          </li>
          <?php
            // simple window
            $start = max(1, $page - 2);
            $end = min($total_pages, $page + 2);
            if ($start > 1) {
              echo '<li class="page-item"><a class="page-link" href="'.$base.'1">1</a></li>';
              if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
            }
            for ($p=$start; $p<=$end; $p++) {
              echo '<li class="page-item '.($p==$page?'active':'').'"><a class="page-link" href="'.$base.$p.'">'.$p.'</a></li>';
            }
            if ($end < $total_pages) {
              if ($end < $total_pages-1) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
              echo '<li class="page-item"><a class="page-link" href="'.$base.$total_pages.'">'.$total_pages.'</a></li>';
            }
          ?>
          <li class="page-item <?= ($page>=$total_pages)?'disabled':''; ?>">
            <a class="page-link" href="<?= $base.$next_page; ?>">Next</a>
          </li>
        </ul>
      </nav>
    <?php endif; ?>

  <?php else: ?>
    <div class="text-center py-5">
      <h4 class="text-muted">No bookings yet</h4>
      <p class="text-muted">Start your journey by booking a trip today!</p>
      <a href="book-trip.php" class="btn btn-gradient">Book a Trip</a>
    </div>
  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({ duration: 800, easing: 'ease-in-out' });

  // Auto hide alerts after 7s
  setTimeout(() => {
    document.querySelectorAll('.alert').forEach(alert => {
      let bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    });
  }, 7000);

  // Cancel → update without reload
  function cancelBooking(id) {
    if (!confirm("Cancel this booking?")) return;
    fetch("my-bookings.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "cancel_id=" + encodeURIComponent(id)
    })
    .then(res => res.text())
    .then(data => {
      if (data.trim() === "cancelled") {
        const card = document.getElementById("booking-card-" + id);
        if (!card) return;

        // Update status badge (only the booking status one)
        const statusBadge = card.querySelector('[data-badge="status"]');
        if (statusBadge) {
          statusBadge.className = "status-badge bg-danger text-white";
          statusBadge.textContent = "Cancelled";
        }

        // Hide cancel button
        const cancelBtn = card.querySelector(".btn-outline-danger");
        if (cancelBtn) cancelBtn.remove();

        // Add the same ❌ delete button (exact same markup/classes)
        if (!card.querySelector(".delete-btn")) {
          const delBtn = document.createElement("button");
          delBtn.className = "delete-btn";
          delBtn.innerHTML = '<i class="bi bi-x"></i>';
          delBtn.onclick = function(){ deleteBooking(id); };
          card.querySelector(".card.booking-card").appendChild(delBtn);
        }
      }
    });
  }

  // Delete cancelled booking permanently (already no reload)
  function deleteBooking(id) {
    if (!confirm("Remove this cancelled booking permanently?")) return;
    fetch("my-bookings.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "delete_id=" + encodeURIComponent(id)
    })
    .then(res => res.text())
    .then(data => {
      if (data.trim() === "deleted") {
        const card = document.getElementById("booking-card-" + id);
        if (card) {
          card.style.opacity = "0";
          setTimeout(() => { card.remove(); }, 300);
        }
      }
    });
  }
</script>
</body>
</html>
