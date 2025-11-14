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

/* ---------------- Delete Trip ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
  require_post_csrf();
  $id = (int)$_POST['id'];

  /* 1. Trip image delete */
  $stmt = $conn->prepare("SELECT image FROM trips WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($row = $res->fetch_assoc()) {
    if (!empty($row['image'])) {
      $imgPath = dirname(__DIR__) . "/" . $row['image'];
      if (file_exists($imgPath)) unlink($imgPath);
    }
  }
  $stmt->close();

  /* 2. Trip plans + unki images delete */
  $planStmt = $conn->prepare("SELECT image FROM expenses_templates WHERE trip_id=?");
  $planStmt->bind_param("i", $id);
  $planStmt->execute();
  $planRes = $planStmt->get_result();
  while ($plan = $planRes->fetch_assoc()) {
    if (!empty($plan['image'])) {
      $planPath = dirname(__DIR__) . "/" . $plan['image'];
      if (file_exists($planPath)) unlink($planPath);
    }
  }
  $planStmt->close();

  // ab plans delete karo
  $delPlans = $conn->prepare("DELETE FROM expenses_templates WHERE trip_id=?");
  $delPlans->bind_param("i", $id);
  $delPlans->execute();
  $delPlans->close();

  /* 3. Trip row delete */
  $stmt = $conn->prepare("DELETE FROM trips WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();

  flash('ok', 'Trip and its plans (with images) deleted successfully.');
  header("Location: admin-trips.php");
  exit;
}

/* ---------------- Add / Update Trip ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($_POST['action'] ?? '', ['add', 'update'])) {
  require_post_csrf();

  $trip_name   = trim($_POST['trip_name'] ?? '');
  $destination = trim($_POST['destination'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $start_date  = $_POST['start_date'] ?? '';
  $end_date    = $_POST['end_date'] ?? '';
  $budget      = (float)($_POST['budget'] ?? 0);
  $id          = (int)($_POST['id'] ?? 0);

  // validations
  $errors = [];
  $today = date("Y-m-d");
  if (!v_required($trip_name)) $errors[] = "Trip name required.";
  if (!v_required($destination)) $errors[] = "Destination required.";
  if (!$start_date || !$end_date || $start_date > $end_date || $start_date < $today)
    $errors[] = "Dates must be today or future, and end must be after start.";
  if (!v_number($budget, 0)) $errors[] = "Budget invalid.";

  if ($errors) {
    flash('err', implode(' ', $errors), 'danger');
    header("Location: admin-trips.php");
    exit;
  }

  // handle image upload
  $imagePath = null;
  if (!empty($_FILES['image']['name'])) {
    $uploadDir = dirname(__DIR__) . "/img/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $filename = time() . "_" . basename($_FILES['image']['name']);
    $target   = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
      $imagePath = "img/" . $filename;

      // 🟢 Purani image delete (sirf update case mein)
      if ($_POST['action'] === 'update' && $id > 0) {
        $oldStmt = $conn->prepare("SELECT image FROM trips WHERE id=?");
        $oldStmt->bind_param("i", $id);
        $oldStmt->execute();
        $oldRes = $oldStmt->get_result();
        if ($oldRow = $oldRes->fetch_assoc()) {
          if (!empty($oldRow['image'])) {
            $oldPath = dirname(__DIR__) . "/" . $oldRow['image'];
            if (file_exists($oldPath)) unlink($oldPath);
          }
        }
      }
    }
  }

  // add / update query
  if ($_POST['action'] === 'add') {
    $stmt = $conn->prepare("INSERT INTO trips 
      (trip_name, destination, image, description, start_date, end_date, budget, created_at) 
      VALUES (?,?,?,?,?,?,?,NOW())");
    $stmt->bind_param("ssssssd", $trip_name, $destination, $imagePath, $description, $start_date, $end_date, $budget);
    $stmt->execute();
    flash('ok', 'Trip added.');
  } else {
    if ($imagePath) {
      $stmt = $conn->prepare("UPDATE trips 
        SET trip_name=?, destination=?, image=?, description=?, start_date=?, end_date=?, budget=? 
        WHERE id=?");
      $stmt->bind_param("ssssssdi", $trip_name, $destination, $imagePath, $description, $start_date, $end_date, $budget, $id);
    } else {
      $stmt = $conn->prepare("UPDATE trips 
        SET trip_name=?, destination=?, description=?, start_date=?, end_date=?, budget=? 
        WHERE id=?");
      $stmt->bind_param("sssssdi", $trip_name, $destination, $description, $start_date, $end_date, $budget, $id);
    }
    $stmt->execute();
    flash('ok', 'Trip updated.');
  }

  header("Location: admin-trips.php");
  exit;
}

/* ---------------- Pagination + Search ---------------- */
$q = trim($_GET['q'] ?? '');
$where = '';
if ($q !== '') {
  $qLike = "%" . $conn->real_escape_string($q) . "%";
  $where = "WHERE trip_name LIKE '$qLike' OR destination LIKE '$qLike' OR description LIKE '$qLike'";
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$totalRes = $conn->query("SELECT COUNT(*) as cnt FROM trips $where");
$totalRows = $totalRes->fetch_assoc()['cnt'];
$totalPages = ceil($totalRows / $limit);

$sql = "SELECT * FROM trips $where ORDER BY id DESC LIMIT $limit OFFSET $offset";
$res = $conn->query($sql);
?>

<style>
/* ---- Responsive Table (Same as Messages Page) ---- */
.table-responsive {
  overflow-x: auto !important;
  -webkit-overflow-scrolling: touch !important;
}

/* Minimum width so table squeeze na ho */
.table-responsive table {
  min-width: 950px !important;
}

/* Table Headings neat */
.table-responsive th {
  white-space: nowrap !important;
  font-weight: 600 !important;
  vertical-align: middle !important;
}

/* Description + Destination wrap ho, layout break na ho */
.table-responsive td {
  white-space: normal !important;
  word-break: break-word !important;
  vertical-align: middle !important;
}

/* Image column ko perfect fix */
.table-responsive td img {
  width: 75px;
  height: 50px;
  object-fit: cover;
  border-radius: 6px;
}

/* Actions buttons one-line hi rahein */
.table-responsive td:last-child {
  white-space: nowrap !important;
}

@media(max-width: 768px) {
  /* Page heading centered on mobile */
  .fw-bold.text-gradient {
    text-align: center;
    width: 100%;
  }
  
  /* Search bar stack view */
  form.d-flex {
    flex-direction: column;
    gap: 10px;
  }
}
</style>

<main class="col-12 col-md-12 col-lg-10 p-4">

  <!-- Title + Add -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold text-gradient" data-aos="fade-right">
      <i class="bi bi-airplane me-2"></i> Trips
    </h2>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#tripModal" data-aos="fade-left">
      <i class="bi bi-plus-lg"></i> Add Trip
    </button>
  </div>

  <!-- 🔍 Search Form -->
  <form class="d-flex align-items-center gap-2 p-2 rounded-4 shadow-sm bg-light my-3" method="get" data-aos="fade-left">
    <div class="input-group input-group-sm flex-grow-1">
      <span class="input-group-text bg-white border-0 rounded-pill ps-3">
        <i class="bi bi-search text-muted"></i>
      </span>
      <input type="text"
             class="form-control form-control-sm border-0 rounded-pill"
             placeholder="Search trip name / destination / description"
             name="q"
             value="<?= htmlspecialchars($q) ?>" maxlength="50">
    </div>

    <button class="btn btn-sm btn-primary rounded-pill px-3">
      <i class="bi bi-search"></i> Search
    </button>

    <?php if ($q): ?>
      <a href="admin-trips.php"
         class="btn btn-sm btn-outline-danger rounded-pill px-3"
         data-aos="fade-left">
        <i class="bi bi-x-circle"></i> Reset
      </a>
    <?php endif; ?>
  </form>

  <!-- Alerts -->
  <div class="fade show">
    <?= flash('ok') ?: '' ?>
    <?= flash('err') ?: '' ?>
  </div>

  <!-- Table -->
  <div class="card shadow border-0 rounded-3" data-aos="fade-up">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Trip Name</th>
            <th>Destination</th>
            <th>Image</th>
            <th>Description</th>
            <th>Dates</th>
            <th>Budget</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($res->num_rows > 0): ?>
          <?php while ($row = $res->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= h($row['trip_name']) ?></td>
              <td><?= h($row['destination']) ?></td>
              <td>
                <?php if (!empty($row['image'])): ?>
                  <img src="../<?= h($row['image']) ?>" class="rounded shadow-sm" style="width:70px;height:45px;object-fit:cover;">
                <?php else: ?>
                  <span class="text-muted">No Image</span>
                <?php endif; ?>
              </td>
              <td><?= h(mb_strimwidth($row['description'], 0, 40, "...")) ?></td>
              <td><?= h($row['start_date']) ?> → <?= h($row['end_date']) ?></td>
              <td>$<?= number_format($row['budget'], 2) ?></td>
              <td><?= h($row['created_at']) ?></td>
              <td>
                <button class="btn btn-sm btn-info"
                  data-bs-toggle="modal" data-bs-target="#tripModal"
                  data-edit='<?= json_encode($row) ?>'>
                  <i class="bi bi-pencil"></i>
                </button>
                <form method="post" class="d-inline" onsubmit="return confirm('Delete this trip?')">
                  <?= csrf_field(); ?>
                  <input type="hidden" name="id" value="<?= $row['id'] ?>">
                  <input type="hidden" name="action" value="delete">
                  <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                </form>
                <a href="admin-trip-plans.php?trip_id=<?= $row['id'] ?>" class="btn btn-sm btn-warning my-1">
                  <i class="bi bi-list-task"></i> Plans
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="9" class="text-center text-muted">No trips found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
  <nav class="mt-3">
    <ul class="pagination justify-content-center">
      <?php if ($page > 1): ?>
        <li class="page-item"><a class="page-link" href="?page=<?= $page-1 ?>">&laquo; Prev</a></li>
      <?php endif; ?>
      <?php for ($i=1; $i<=$totalPages; $i++): ?>
        <li class="page-item <?= ($i == $page ? 'active' : '') ?>">
          <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      <?php if ($page < $totalPages): ?>
        <li class="page-item"><a class="page-link" href="?page=<?= $page+1 ?>">Next &raquo;</a></li>
      <?php endif; ?>
    </ul>
  </nav>
  <?php endif; ?>

</main>

<!-- Trip Modal -->
<div class="modal fade" id="tripModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form method="post" enctype="multipart/form-data" class="modal-content">
      <?= csrf_field(); ?>
      <input type="hidden" name="id" id="trip_id">
      <input type="hidden" name="action" id="trip_action" value="add">

      <div class="modal-header bg-light">
        <h5 class="modal-title text-dark">Add Trip</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body text-dark">
        <div class="mb-3">
          <label class="form-label">Trip Name</label>
          <input type="text" class="form-control" name="trip_name" id="trip_name" required minlength="3" maxlength="40" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">
        </div>
        <div class="mb-3">
          <label class="form-label">Destination</label>
          <input type="text" class="form-control" name="destination" id="trip_destination" required minlength="4" maxlength="40" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea class="form-control" name="description" id="trip_description" rows="3" minlength="5" maxlength="250"></textarea>
        </div>
        <div class="row">
          <div class="col">
            <label class="form-label">Start Date</label>
            <input type="date" class="form-control" name="start_date" id="trip_start" min="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="col">
            <label class="form-label">End Date</label>
            <input type="date" class="form-control" name="end_date" id="trip_end" min="<?= date('Y-m-d') ?>" required>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-6">
            <label class="form-label">Budget</label>
            <input type="number" class="form-control" step="1" name="budget" id="trip_budget" required max="999999">
          </div>
          <div class="col-md-6">
            <label class="form-label">Image</label>
            <input type="file" class="form-control" name="image" id="trip_image">
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-success shadow-sm">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
const tripModal = document.getElementById('tripModal');
tripModal.addEventListener('show.bs.modal', e => {
  const btn = e.relatedTarget;
  const data = btn?.getAttribute('data-edit');
  const form = tripModal.querySelector('form');
  if (data) {
    const row = JSON.parse(data);
    form.trip_action.value = 'update';
    form.trip_id.value = row.id;
    form.trip_name.value = row.trip_name;
    form.trip_destination.value = row.destination;
    form.trip_description.value = row.description ?? '';
    form.trip_start.value = row.start_date;
    form.trip_end.value = row.end_date;
    form.trip_budget.value = row.budget;
    tripModal.querySelector('.modal-title').innerText = "Edit Trip";
  } else {
    form.reset();
    form.trip_action.value = 'add';
    form.trip_id.value = '';
    tripModal.querySelector('.modal-title').innerText = "Add Trip";
  }
});

// auto close alerts
setTimeout(() => {
  document.querySelectorAll('.alert').forEach(el => {
    el.classList.add("animate__animated", "animate__fadeOutUp");
    setTimeout(() => el.remove(), 800);
  });
}, 5000);
</script>

<?php
require_once "inc/admin-footer.php";
ob_end_flush();
?>
