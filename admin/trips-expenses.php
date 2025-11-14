<?php
ob_start();
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: admin-login.php");
  exit;
}

require_once "connect.php";
require_once "helpers.php";
require_once "inc/admin-header.php";
require_once "inc/admin-sidebar.php";

$trip_id = (int)($_GET['trip_id'] ?? 0);
$user_id = (int)($_GET['user_id'] ?? 0);

if ($trip_id <= 0 || $user_id <= 0) {
    echo "<div class='alert alert-danger m-3'>Invalid Request</div>";
    require_once "inc/admin-footer.php";
    exit;
}

// trip info
$trip = $conn->query("SELECT * FROM trips WHERE id=$trip_id")->fetch_assoc();
// user info
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

/* --- Search + Pagination --- */
$q = trim($_GET['q'] ?? '');
$where = "WHERE trip_id=$trip_id AND user_id=$user_id";
if ($q !== '') {
  $qLike = "%".$conn->real_escape_string($q)."%";
  $where .= " AND (category LIKE '$qLike' OR notes LIKE '$qLike')";
}

$limit = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page-1)*$limit;

$total = $conn->query("SELECT COUNT(*) cnt FROM expenses $where")->fetch_assoc()['cnt'];
$totalPages = ceil($total/$limit);

$sql = "SELECT * FROM expenses $where ORDER BY expense_date ASC LIMIT $limit OFFSET $offset";
$res = $conn->query($sql);
?>

<main class="col-12 col-md-12 col-lg-10 p-4">

  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" data-aos="fade-down">
  <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
    <li class="breadcrumb-item">
      <a href="dashboard.php">
        <i class="bi bi-house-door crumb-dashboard"></i> Dashboard
      </a>
    </li>
    <li class="breadcrumb-item">
      <a href="admin-expenses.php">
        <i class="bi bi-wallet2 crumb-expenses"></i> Expenses
      </a>
    </li>
    <li class="breadcrumb-item">
      <a href="users-expenses-trips.php?user_id=<?=$user_id?>">
        <i class="bi bi-person crumb-user"></i> <?=h($user['first_name']." ".$user['last_name'])?>
      </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
      <i class="bi bi-suitcase"></i> <?=h($trip['trip_name'])?>
    </li>
  </ol>
</nav>

  <h2 class="fw-bold text-gradient mb-3" data-aos="fade-right">
    <i class="bi bi-cash"></i> Expenses of <?=h($user['first_name']." ".$user['last_name'])?> for <?=h($trip['trip_name'])?>
  </h2>

  <!-- Search -->
  <form class="d-flex align-items-center gap-2 p-2 rounded-4 shadow-sm bg-light mb-3" method="get" data-aos="fade-left">
    <input type="hidden" name="trip_id" value="<?=$trip_id?>">
    <input type="hidden" name="user_id" value="<?=$user_id?>">
    <div class="input-group input-group-sm flex-grow-1">
      <span class="input-group-text bg-white border-0 rounded-pill ps-3"><i class="bi bi-search text-muted"></i></span>
      <input type="text" name="q" value="<?=h($q)?>" class="form-control border-0 rounded-pill" placeholder="Search category/notes" maxlength="50">
    </div>
    <button class="btn btn-sm btn-primary rounded-pill px-3"><i class="bi bi-search"></i> Search</button>
    <?php if ($q): ?><a href="trips-expenses.php?trip_id=<?=$trip_id?>&user_id=<?=$user_id?>" class="btn btn-sm btn-outline-danger rounded-pill px-3">Reset</a><?php endif; ?>
  </form>

  <div class="card shadow border-0 rounded-3" data-aos="fade-up">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr><th>ID</th><th>Category</th><th>Amount</th><th>Date</th><th>Notes</th><th>Image</th></tr>
        </thead>
        <tbody>
        <?php if ($res->num_rows): while($e=$res->fetch_assoc()): ?>
          <tr>
            <td><?=$e['id']?></td>
            <td><?=h($e['category'])?></td>
            <td>$<?=number_format($e['amount'],2)?></td>
            <td><?=h($e['expense_date'])?></td>
            <td><?=h(mb_strimwidth($e['notes'],0,50,"..."))?></td>
            <td>
              <?php if($e['image']): ?>
                <img src="../<?=h($e['image'])?>" style="width:70px;height:45px;object-fit:cover">
              <?php else: ?>
                <span class="text-muted">No image</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="6" class="text-center text-muted">No expenses found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages>1): ?>
    <nav class="mt-3"><ul class="pagination justify-content-center">
      <?php if ($page>1): ?><li class="page-item"><a class="page-link" href="?trip_id=<?=$trip_id?>&user_id=<?=$user_id?>&page=<?=$page-1?>&q=<?=h($q)?>">&laquo; Prev</a></li><?php endif; ?>
      <?php for($i=1;$i<=$totalPages;$i++): ?>
        <li class="page-item <?=$i==$page?'active':''?>"><a class="page-link" href="?trip_id=<?=$trip_id?>&user_id=<?=$user_id?>&page=<?=$i?>&q=<?=h($q)?>"><?=$i?></a></li>
      <?php endfor; ?>
      <?php if ($page<$totalPages): ?><li class="page-item"><a class="page-link" href="?trip_id=<?=$trip_id?>&user_id=<?=$user_id?>&page=<?=$page+1?>&q=<?=h($q)?>">Next &raquo;</a></li><?php endif; ?>
    </ul></nav>
  <?php endif; ?>
</main>

<?php require_once "inc/admin-footer.php"; ob_end_flush(); ?>

<style>
  .breadcrumb {
    margin-bottom: 1rem;
  }
  .breadcrumb .breadcrumb-item a {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
    background: #f8f9fa;
    transition: all 0.2s ease;
  }
  .breadcrumb .breadcrumb-item a:hover {
    background: #e9ecef;
    text-decoration: none;
  }
  .breadcrumb .bi {
    margin-right: 6px;
    font-size: 1rem;
  }
  .crumb-dashboard { color: #0d6efd; }   /* Blue */
  .crumb-expenses  { color: #198754; }   /* Green */
  .crumb-user      { color: #0dcaf0; }   /* Cyan */
  .crumb-trip      { color: #fd7e14; }   /* Orange */
  .breadcrumb-item.active {
    background: #dee2e6;
    border-radius: 20px;
    padding: 6px 12px;
    font-weight: 600;
  }
  /* === TABLE RESPONSIVE SAME LIKE OTHER PAGES === */
.table-responsive {
  overflow-x: auto !important;
  -webkit-overflow-scrolling: touch !important;
}

.table {
  white-space: nowrap !important; /* Table columns wrap na hon */
}

.table th, .table td {
  vertical-align: middle;
}

/* Large text (notes) ko readable rakhna */
.table td:nth-child(5) {
  max-width: 250px;
  white-space: normal !important;
  word-wrap: break-word;
}

/* Action column image nowrap */
.table td:last-child {
  white-space: nowrap !important;
}

/* === SEARCH BAR MOBILE FIX === */
@media (max-width: 768px) {
  form.d-flex {
    flex-wrap: wrap !important;
  }

  form.d-flex .input-group {
    width: 100% !important;
  }

  form.d-flex button,
  form.d-flex a {
    width: 100% !important;
    margin-top: 6px;
  }
}

/* === HEADING CENTER ON SMALL SCREENS === */
@media (max-width: 576px) {
  h2.text-gradient {
    text-align: center !important;
    width: 100%;
    font-size: 1.25rem !important;
  }
}

/* Breadcrumb compact on small screens */
@media (max-width: 576px) {
  .breadcrumb {
    font-size: 13px !important;
  }
}

</style>

