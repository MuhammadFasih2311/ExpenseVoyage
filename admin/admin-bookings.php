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

/* --- Search + Pagination --- */
$q = trim($_GET['q'] ?? '');
$where = '';
if ($q !== '') {
  $qLike = "%".$conn->real_escape_string($q)."%";
  $where = "WHERE u.first_name LIKE '$qLike' OR u.last_name LIKE '$qLike' OR u.email LIKE '$qLike'";
}

$limit = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page-1) * $limit;

$total = $conn->query("SELECT COUNT(DISTINCT u.id) cnt 
                       FROM users u JOIN bookings b ON b.user_id=u.id $where")->fetch_assoc()['cnt'];
$totalPages = ceil($total/$limit);

$sql = "SELECT DISTINCT u.id, u.first_name, u.last_name, u.email
        FROM users u
        JOIN bookings b ON b.user_id = u.id
        $where
        ORDER BY u.id DESC LIMIT $limit OFFSET $offset";
$res = $conn->query($sql);
?>
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
  .crumb-trip      { color: #fd7e14; }   /* Orange */
  .breadcrumb-item.active {
    background: #dee2e6;
    border-radius: 20px;
    padding: 6px 12px;
    font-weight: 600;
  }

  /* === TABLE SAME ON SMALL SCREENS === */
  .table-responsive {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch !important;
  }

  .table {
    white-space: nowrap !important;
  }

  .table-responsive table {
    min-width: 700px !important;
  }

  .table th, .table td {
    vertical-align: middle !important;
  }

  /* Allow email to break properly */
  .table td:nth-child(3) {
    white-space: normal !important;
    word-break: break-word !important;
  }

  /* Action button no wrap */
  .table td:last-child {
    white-space: nowrap !important;
  }

  /* === HEADING + SEARCH RESPONSIVE FIX === */
  @media(max-width: 768px){
    
    /* Heading center */
    h2.text-gradient {
      text-align: center !important;
      width: 100%;
      font-size: 1.25rem !important;
    }

    /* Search bar vertical */
    form.d-flex {
      flex-direction: column !important;
      gap: 10px !important;
    }

    form.d-flex button,
    form.d-flex a {
      width: 100% !important;
    }
  }
</style>

<main class="col-12 col-md-12 col-lg-10 p-4">

  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" data-aos="fade-down">
    <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
      <li class="breadcrumb-item">
        <a href="dashboard.php"><i class="bi bi-house-door crumb-dashboard"></i> Dashboard</a>
      </li>
      <li class="breadcrumb-item active"><i class="bi bi-journal-bookmark crumb-trip"></i> Bookings</li>
    </ol>
  </nav>

  <h2 class="fw-bold text-gradient mb-3" data-aos="fade-right"><i class="bi bi-people"></i> Users with Bookings</h2>

  <!-- Search -->
  <form class="d-flex align-items-center gap-2 p-2 rounded-4 shadow-sm bg-light mb-3" method="get" data-aos="fade-left">
    <div class="input-group input-group-sm flex-grow-1">
      <span class="input-group-text bg-white border-0 rounded-pill ps-3"><i class="bi bi-search text-muted"></i></span>
      <input type="text" name="q" value="<?=h($q)?>" class="form-control border-0 rounded-pill" placeholder="Search user/email" maxlength="50">
    </div>
    <button class="btn btn-sm btn-primary rounded-pill px-3"><i class="bi bi-search"></i> Search</button>
    <?php if ($q): ?><a href="admin-bookings.php" class="btn btn-sm btn-outline-danger rounded-pill px-3">Reset</a><?php endif; ?>
  </form>

  <div class="card shadow border-0 rounded-3" data-aos="fade-up">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>ID</th><th>Name</th><th>Email</th><th>Action</th></tr></thead>
        <tbody>
        <?php if ($res->num_rows): while($u=$res->fetch_assoc()): ?>
          <tr>
            <td><?=$u['id']?></td>
            <td><?=h($u['first_name'].' '.$u['last_name'])?></td>
            <td><?=h($u['email'])?></td>
            <td><a href="users-bookings-trips.php?user_id=<?=$u['id']?>" class="btn btn-sm btn-primary"><i class="bi bi-suitcase"></i> View Trips</a></td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="4" class="text-center text-muted">No users found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages>1): ?>
    <nav class="mt-3"><ul class="pagination justify-content-center">
      <?php if ($page>1): ?><li class="page-item"><a class="page-link" href="?page=<?=$page-1?>&q=<?=h($q)?>">&laquo; Prev</a></li><?php endif; ?>
      <?php for($i=1;$i<=$totalPages;$i++): ?>
        <li class="page-item <?=$i==$page?'active':''?>"><a class="page-link" href="?page=<?=$i?>&q=<?=h($q)?>"><?=$i?></a></li>
      <?php endfor; ?>
      <?php if ($page<$totalPages): ?><li class="page-item"><a class="page-link" href="?page=<?=$page+1?>&q=<?=h($q)?>">Next &raquo;</a></li><?php endif; ?>
    </ul></nav>
  <?php endif; ?>
</main>

<?php require_once "inc/admin-footer.php"; ob_end_flush(); ?>
