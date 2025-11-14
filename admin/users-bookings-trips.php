<?php
ob_start();
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: admin-login.php"); exit; }

require_once "connect.php";
require_once "helpers.php";
require_once "inc/admin-header.php";
require_once "inc/admin-sidebar.php";

$user_id = (int)($_GET['user_id'] ?? 0);
if ($user_id <= 0) { echo "<div class='alert alert-danger m-3'>Invalid User ID</div>"; require_once "inc/admin-footer.php"; exit; }
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

/* --- Filters --- */
$status_filter = $_GET['status'] ?? 'all';
$payment_filter = $_GET['payment'] ?? 'all';

$whereStatus = $status_filter !== 'all' ? " AND b.status='".$conn->real_escape_string($status_filter)."'" : '';
$wherePayment = $payment_filter !== 'all' ? " AND b.payment_status='".$conn->real_escape_string($payment_filter)."'" : '';

/* --- Pagination --- */
$limit = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page-1)*$limit;

$total = $conn->query("SELECT COUNT(DISTINCT t.id) cnt 
                       FROM trips t 
                       JOIN bookings b ON b.trip_id=t.id AND b.user_id=$user_id
                       WHERE 1=1 $whereStatus $wherePayment")->fetch_assoc()['cnt'];
$totalPages = ceil($total/$limit);

$sql = "SELECT DISTINCT t.id, t.trip_name, t.destination, t.start_date, t.end_date
        FROM trips t
        JOIN bookings b ON b.trip_id = t.id
        WHERE b.user_id=$user_id $whereStatus $wherePayment
        ORDER BY t.id DESC LIMIT $limit OFFSET $offset";
$res = $conn->query($sql);
?>
<style>
/* Make table same layout on small screens (no breaking rows) */
.table {
  white-space: nowrap !important;
}

.table-responsive {
  overflow-x: auto !important;
  -webkit-overflow-scrolling: touch !important;
}

.table-responsive table {
  min-width: 700px !important;
}

/* Table Headings neat */
.table-responsive th {
  white-space: nowrap !important;
  font-weight: 600;
}

/* Allow wrapping for text */
.table-responsive td {
  white-space: normal !important;
  word-break: break-word !important;
}

/* Buttons inline */
.table-responsive td:last-child {
  white-space: nowrap !important;
}

/* ----- Mobile Fixes ----- */
@media(max-width: 768px){

  /* Heading center */
  h2.text-gradient {
    text-align: center !important;
    width: 100%;
    font-size: 1.25rem !important;
  }

  /* Filters vertical */
  .d-flex.flex-wrap.gap-2 form {
    width: 100%;
  }

  .d-flex.flex-wrap.gap-2 form select,
  .d-flex.flex-wrap.gap-2 form button,
  .d-flex.flex-wrap.gap-2 form a {
    width: 100% !important;
  }

}
</style>

<main class="col-12 col-md-12 col-lg-10 p-4">

  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" data-aos="fade-down">
    <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
      <li class="breadcrumb-item"><a href="dashboard.php"><i class="bi bi-house-door crumb-dashboard"></i> Dashboard</a></li>
      <li class="breadcrumb-item"><a href="admin-bookings.php"><i class="bi bi-journal-bookmark crumb-trip"></i> Bookings</a></li>
      <li class="breadcrumb-item active"><i class="bi bi-person crumb-user"></i> <?=h($user['first_name']." ".$user['last_name'])?></li>
    </ol>
  </nav>

  <h2 class="fw-bold text-gradient mb-3" data-aos="fade-right">
    <i class="bi bi-suitcase"></i> Trips of <?=h($user['first_name']." ".$user['last_name'])?>
  </h2>

  <!-- Filters -->
  <div class="d-flex flex-wrap gap-2 mb-3" data-aos="fade-left">
    <form method="get" class="d-flex flex-wrap gap-2">
      <input type="hidden" name="user_id" value="<?=$user_id?>">

      <select name="status" class="form-select form-select-sm">
        <option value="all" <?=$status_filter=='all'?'selected':''?>>All Status</option>
        <option value="pending" <?=$status_filter=='pending'?'selected':''?>>Pending</option>
        <option value="confirmed" <?=$status_filter=='confirmed'?'selected':''?>>Confirmed</option>
        <option value="cancelled" <?=$status_filter=='cancelled'?'selected':''?>>Cancelled</option>
      </select>

      <select name="payment" class="form-select form-select-sm">
        <option value="all" <?=$payment_filter=='all'?'selected':''?>>All Payments</option>
        <option value="paid" <?=$payment_filter=='paid'?'selected':''?>>Paid</option>
        <option value="unpaid" <?=$payment_filter=='unpaid'?'selected':''?>>Unpaid</option>
        <option value="refunded" <?=$payment_filter=='refunded'?'selected':''?>>Refunded</option>
      </select>

      <button class="btn btn-sm btn-primary"><i class="bi bi-filter"></i> Apply</button>
      <a href="users-bookings-trips.php?user_id=<?=$user_id?>" class="btn btn-sm btn-outline-secondary">Reset</a>
    </form>
  </div>

  <!-- Table -->
  <div class="card shadow border-0 rounded-3" data-aos="fade-up">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>ID</th><th>Trip Name</th><th>Destination</th><th>Dates</th><th>Action</th></tr></thead>
        <tbody>
        <?php if ($res->num_rows): while($t=$res->fetch_assoc()): ?>
          <tr>
            <td><?=$t['id']?></td>
            <td><?=h($t['trip_name'])?></td>
            <td><?=h($t['destination'])?></td>
            <td><?=h($t['start_date'])?> → <?=h($t['end_date'])?></td>
            <td><a href="trips-bookings.php?trip_id=<?=$t['id']?>&user_id=<?=$user_id?>" class="btn btn-sm btn-warning"><i class="bi bi-people"></i> View Bookings</a></td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="5" class="text-center text-muted">No trips found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages>1): ?>
    <nav class="mt-3"><ul class="pagination justify-content-center">
      <?php if ($page>1): ?><li class="page-item"><a class="page-link" href="?user_id=<?=$user_id?>&status=<?=$status_filter?>&payment=<?=$payment_filter?>&page=<?=$page-1?>">&laquo; Prev</a></li><?php endif; ?>
      <?php for($i=1;$i<=$totalPages;$i++): ?>
        <li class="page-item <?=$i==$page?'active':''?>"><a class="page-link" href="?user_id=<?=$user_id?>&status=<?=$status_filter?>&payment=<?=$payment_filter?>&page=<?=$i?>"><?=$i?></a></li>
      <?php endfor; ?>
      <?php if ($page<$totalPages): ?><li class="page-item"><a class="page-link" href="?user_id=<?=$user_id?>&status=<?=$status_filter?>&payment=<?=$payment_filter?>&page=<?=$page+1?>">Next &raquo;</a></li><?php endif; ?>
    </ul></nav>
  <?php endif; ?>
</main>

<?php require_once "inc/admin-footer.php"; ob_end_flush(); ?>
