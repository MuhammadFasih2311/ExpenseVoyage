<?php
ob_start();
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: admin-login.php"); exit; }

require_once "connect.php";
require_once "helpers.php";
require_once "inc/admin-header.php";
require_once "inc/admin-sidebar.php";

$trip_id = (int)($_GET['trip_id'] ?? 0);
$user_id = (int)($_GET['user_id'] ?? 0);
if ($trip_id <= 0 || $user_id <= 0) { echo "<div class='alert alert-danger m-3'>Invalid Request</div>"; require_once "inc/admin-footer.php"; exit; }

$trip = $conn->query("SELECT * FROM trips WHERE id=$trip_id")->fetch_assoc();
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

/* --- Pagination --- */
$limit = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page-1)*$limit;

$total = $conn->query("SELECT COUNT(*) cnt FROM bookings WHERE trip_id=$trip_id AND user_id=$user_id")->fetch_assoc()['cnt'];
$totalPages = ceil($total/$limit);

$sql = "SELECT * FROM bookings WHERE trip_id=$trip_id AND user_id=$user_id ORDER BY booking_date DESC LIMIT $limit OFFSET $offset";
$res = $conn->query($sql);
?>

<style>
/* Keep table layout clean */
.table {
  white-space: nowrap !important;
}

.table-responsive {
  overflow-x: auto !important;
  -webkit-overflow-scrolling: touch !important;
}

.table-responsive table {
  min-width: 900px !important;
}

/* Wrap text everywhere except actions cell */
.table-responsive td {
  white-space: normal !important;
  word-break: break-word !important;
}

/* ✅ Actions column multi-row allowed */
.table-responsive td.actions-cell {
  white-space: normal !important;
}

.table-responsive td.actions-cell .btn {
  margin: 2px 0 !important;
  width: 100% !important;
}

/* Desktop: keep buttons inline */
@media(min-width: 992px){
  .table-responsive td.actions-cell .btn {
    width: auto !important;
    display: inline-block !important;
  }
}

/* Mobile heading center */
@media(max-width: 768px){
  h2.text-gradient { text-align: center; font-size: 1.2rem; }
}
</style>


<main class="col-12 col-md-12 col-lg-10 p-4">

  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" data-aos="fade-down">
    <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
      <li class="breadcrumb-item"><a href="dashboard.php"><i class="bi bi-house-door crumb-dashboard"></i> Dashboard</a></li>
      <li class="breadcrumb-item"><a href="admin-bookings.php"><i class="bi bi-journal-bookmark crumb-trip"></i> Bookings</a></li>
      <li class="breadcrumb-item"><a href="users-bookings-trips.php?user_id=<?=$user_id?>"><i class="bi bi-person crumb-user"></i> <?=h($user['first_name']." ".$user['last_name'])?></a></li>
      <li class="breadcrumb-item active"><i class="bi bi-suitcase"></i> <?=h($trip['trip_name'])?></li>
    </ol>
  </nav>

  <h2 class="fw-bold text-gradient mb-3" data-aos="fade-right">
    <i class="bi bi-clipboard-check"></i> Bookings of <?=h($user['first_name']." ".$user['last_name'])?> for <?=h($trip['trip_name'])?>
  </h2>

  <div class="card shadow border-0 rounded-3" data-aos="fade-up">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ID</th><th>Persons</th><th>Status</th><th>Payment</th><th>Txn ID</th><th>Notes</th><th>Date</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($res->num_rows): while($b=$res->fetch_assoc()): ?>
          <tr>
            <td><?=$b['id']?></td>
            <td><?=$b['num_persons']?></td>
            <td><span class="badge bg-<?php 
              if($b['status']=='confirmed') echo 'success'; 
              elseif($b['status']=='cancelled') echo 'danger'; 
              else echo 'secondary';
            ?>"><?=ucfirst($b['status'])?></span></td>
            <td><span class="badge bg-<?php 
              if($b['payment_status']=='paid') echo 'success';
              elseif($b['payment_status']=='refunded') echo 'warning';
              else echo 'dark';
            ?>"><?=ucfirst($b['payment_status'])?></span></td>
            <td><?=h($b['transaction_id'] ?: '-')?></td>
            <td><?=h(mb_strimwidth($b['notes'],0,50,"..."))?></td>
            <td><?=h($b['booking_date'])?></td>
            <td class="actions-cell">
              <!-- Status actions -->
              <?php if($b['status']!='confirmed'): ?>
                <a href="booking-action.php?action=confirm&id=<?=$b['id']?>&trip_id=<?=$trip_id?>&user_id=<?=$user_id?>" class="btn btn-sm btn-success">Confirm</a>
              <?php endif; ?>
              <?php if($b['status']!='pending'): ?>
                <a href="booking-action.php?action=pending&id=<?=$b['id']?>&trip_id=<?=$trip_id?>&user_id=<?=$user_id?>" class="btn btn-sm btn-secondary mx-2 my-2">Pending</a>
              <?php endif; ?>
              <?php if($b['status']!='cancelled'): ?>
                <a href="booking-action.php?action=cancel&id=<?=$b['id']?>&trip_id=<?=$trip_id?>&user_id=<?=$user_id?>" class="btn btn-sm btn-warning my-2">Cancel</a>
              <?php endif; ?>
              <a href="booking-action.php?action=delete&id=<?=$b['id']?>&trip_id=<?=$trip_id?>&user_id=<?=$user_id?>" onclick="return confirm('Delete this booking?')" class="btn btn-sm btn-danger">Delete</a>

              <!-- ✅ Payment actions -->
              <?php if($b['payment_status']!='paid'): ?>
                <a href="booking-action.php?action=paid&id=<?=$b['id']?>&trip_id=<?=$trip_id?>&user_id=<?=$user_id?>" class="btn btn-sm btn-success my-2">Mark Paid</a>
              <?php endif; ?>
              <?php if($b['payment_status']!='unpaid'): ?>
                <a href="booking-action.php?action=unpaid&id=<?=$b['id']?>&trip_id=<?=$trip_id?>&user_id=<?=$user_id?>" class="btn btn-sm btn-dark my-2">Mark Unpaid</a>
              <?php endif; ?>
              <?php if($b['payment_status']!='refunded'): ?>
                <a href="booking-action.php?action=refunded&id=<?=$b['id']?>&trip_id=<?=$trip_id?>&user_id=<?=$user_id?>" class="btn btn-sm btn-warning my-2">Refund</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="8" class="text-center text-muted">No bookings found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages>1): ?>
    <nav class="mt-3"><ul class="pagination justify-content-center">
      <?php if ($page>1): ?><li class="page-item"><a class="page-link" href="?trip_id=<?=$trip_id?>&user_id=<?=$user_id?>&page=<?=$page-1?>">&laquo; Prev</a></li><?php endif; ?>
      <?php for($i=1;$i<=$totalPages;$i++): ?>
        <li class="page-item <?=$i==$page?'active':''?>"><a class="page-link" href="?trip_id=<?=$trip_id?>&user_id=<?=$user_id?>&page=<?=$i?>"><?=$i?></a></li>
      <?php endfor; ?>
      <?php if ($page<$totalPages): ?><li class="page-item"><a class="page-link" href="?trip_id=<?=$trip_id?>&user_id=<?=$user_id?>&page=<?=$page+1?>">Next &raquo;</a></li><?php endif; ?>
    </ul></nav>
  <?php endif; ?>
</main>

<?php require_once "inc/admin-footer.php"; ob_end_flush(); ?>
