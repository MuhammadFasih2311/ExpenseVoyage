<?php
ob_start();   // output buffering start
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: admin-login.php");
  exit;
}
require_once "connect.php";
require_once "helpers.php";
require_once "inc/admin-header.php";
require_once "inc/admin-sidebar.php";

/* Delete */
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '')==='delete') {
  require_post_csrf();
  $id = (int)$_POST['id'];
  $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
  $stmt->bind_param("i",$id);
  $stmt->execute();
  flash('ok','User deleted.');
  header("Location: users.php"); exit;
}

/* Update */
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '')==='update') {
  require_post_csrf();
  $id = (int)$_POST['id'];
  $first = trim($_POST['first_name']??'');
  $last  = trim($_POST['last_name']??'');
  $email = trim($_POST['email']??'');

  $errors=[];
  if(!v_required($first) || !v_len($first,2,50)) $errors[]="First name invalid.";
  if(!v_required($last) || !v_len($last,2,50)) $errors[]="Last name invalid.";
  if(!v_email($email)) $errors[]="Email invalid.";

  if($errors){
    flash('err', implode(' ', $errors),'danger');
  }else{
    $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=? WHERE id=?");
    $stmt->bind_param("sssi",$first,$last,$email,$id);
    if($stmt->execute()){
        flash('ok','User updated.');
    } else {
        flash('err',"Error: ".$stmt->error,'danger');
    }
  }
  header("Location: users.php"); exit;
}

/* Search + Pagination + Sorting */
$q = trim($_GET['q'] ?? '');
$page = (int)($_GET['page'] ?? 1);
$per_page = 10;

$sort = $_GET['sort'] ?? 'id';
$order = $_GET['order'] ?? 'desc';
$allowed = ['id','first_name','last_name','email','created_at'];
if(!in_array($sort,$allowed)) $sort='id';
$order = strtolower($order)==='asc' ? 'ASC' : 'DESC';

$where = " WHERE 1 ";
$params=[]; $types='';
if($q!==''){
  $where .= " AND (
    first_name LIKE CONCAT('%',?,'%') 
    OR last_name LIKE CONCAT('%',?,'%') 
    OR email LIKE CONCAT('%',?,'%')
    OR CONCAT(first_name,' ',last_name) LIKE CONCAT('%',?,'%')
  )";
  $params = [$q,$q,$q,$q]; 
  $types='ssss';
}

$count_sql = "SELECT COUNT(*) c FROM users".$where;
$stmt = $conn->prepare($count_sql);
if($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = (int)$stmt->get_result()->fetch_assoc()['c'];

list($page,$pages,$offset) = paginate($total,$page,$per_page);

$sql = "SELECT id, first_name, last_name, email, created_at 
        FROM users $where ORDER BY $sort $order LIMIT ?,?";
$stmt = $conn->prepare($sql);
if($types){
  $types.='ii'; $params[]=$offset; $params[]=$per_page;
  $stmt->bind_param($types, ...$params);
}else{
  $stmt->bind_param('ii',$offset,$per_page);
}
$stmt->execute();
$res = $stmt->get_result();

function sort_link($col,$label,$sort,$order,$q){
  $newOrder = ($sort==$col && $order=='ASC')?'desc':'asc';
  $icon = '';
  if($sort==$col){
    $icon = $order=='ASC' ? '↑' : '↓';
  }
  $url = "users.php?sort=$col&order=$newOrder".($q?"&q=".urlencode($q):"");
  return "<a href='$url' class='text-decoration-none'>$label $icon</a>";
}
?>
<style>
/* Responsive table (same as messages page) */
.table-responsive {
  overflow-x: auto !important;
  -webkit-overflow-scrolling: touch !important;
}

/* Table minimum size maintain ho jaye */
#users-table {
  min-width: 900px !important;
}

/* Table Headings clean, single line */
#users-table th {
  white-space: nowrap !important;
  font-weight: 600 !important;
}

/* Data wrap ho but layout break na ho */
#users-table td {
  white-space: normal !important;
  word-break: break-word !important;
  vertical-align: middle !important;
}

/* Action column same line me rakho */
#users-table td.text-end {
  white-space: nowrap !important;
}
</style>

<main class="col-12 col-md-12 col-lg-10 p-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-gradient mb-0" data-aos="fade-right">
      <i class="bi bi-people me-2"></i> Manage Users
    </h2>
  </div>
  <form class="d-flex align-items-center gap-2 p-2 rounded-4 shadow-sm bg-light my-3" method="get" data-aos="fade-left">
  <div class="input-group input-group-sm flex-grow-1">
    <span class="input-group-text bg-white border-0 rounded-pill ps-3">
      <i class="bi bi-search text-muted"></i>
    </span>
    <input type="text" 
           class="form-control form-control-sm border-0 rounded-pill" 
           placeholder="Search name / email" 
           name="q" 
           value="<?= htmlspecialchars($q) ?>" maxlength="35">
  </div>

  <button class="btn btn-sm btn-primary rounded-pill px-3">
    <i class="bi bi-search"></i> Search
  </button>

  <?php if($q): ?>
    <a href="users.php" 
       class="btn btn-sm btn-outline-danger rounded-pill px-3" 
       data-aos="fade-left">
      <i class="bi bi-x-circle"></i> Reset
    </a>
  <?php endif; ?>
</form>

  <div data-aos="zoom-in" data-aos-delay="50">
    <?= flash('ok') ?: '' ?>
    <?= flash('err') ?: '' ?>
  </div>
<h4 class="fw-bold mb-3" data-aos="fade-right">
  <i class="bi bi-table me-2"></i> Users List
</h4>

  <div class="card shadow-lg border-0 rounded-4 overflow-hidden" data-aos="fade-up" data-aos-delay="100">
    <div class="table-responsive">
    <table class="table align-middle table-hover" id="users-table">
        <thead class="table-light">
          <tr>
            <th scope="col"><?= sort_link('id','ID',$sort,$order,$q) ?></th>
            <th scope="col"><?= sort_link('first_name','Name',$sort,$order,$q) ?></th>
            <th scope="col"><?= sort_link('email','Email',$sort,$order,$q) ?></th>
            <th scope="col"><?= sort_link('created_at','Joined',$sort,$order,$q) ?></th>
            <th scope="col" class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while($u = $res->fetch_assoc()): ?>
            <tr>
              <td class="fw-semibold"><?= $u['id'] ?></td>
              <td><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td><span class="badge bg-light text-dark">
                <?= date("M d, Y", strtotime($u['created_at'])) ?>
              </span></td>
              <td class="text-end">
                <!-- Edit -->
                <button class="btn btn-sm btn-warning rounded-circle me-1"
                  title="Edit"
                  data-bs-toggle="modal" data-bs-target="#editUserModal"
                  data-id="<?= $u['id'] ?>"
                  data-first="<?= htmlspecialchars($u['first_name']) ?>"
                  data-last="<?= htmlspecialchars($u['last_name']) ?>"
                  data-email="<?= htmlspecialchars($u['email']) ?>">
                  <i class="bi bi-pencil"></i>
                </button>

                <!-- Delete -->
                <form method="post" class="d-inline" 
                      onsubmit="return confirm('Are you sure you want to delete this user?');">
                  <?= csrf_field(); ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $u['id'] ?>">
                  <button class="btn btn-sm btn-danger rounded-circle my-2" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-4">
    <?= render_pager($page,$pages) ?>
  </div>
</main>

<!-- Edit Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form method="post" class="modal-content rounded-4 shadow">
      <?= csrf_field(); ?>
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="id" id="edit-id">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold text-dark" data-aos="fade-down" data-aos-delay="100">
          <i class="bi bi-pencil-square me-2"></i> Edit User
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3" data-aos="fade-up" data-aos-delay="150">
          <label class="form-label fw-semibold text-success">First Name</label>
          <input type="text" class="form-control rounded-pill" 
                 name="first_name" id="edit-first" required minlength="3" maxlength="35" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">
        </div>
        <div class="mb-3" data-aos="fade-up" data-aos-delay="200">
          <label class="form-label fw-semibold text-success">Last Name</label>
          <input type="text" class="form-control rounded-pill" 
                 name="last_name" id="edit-last" required minlength="3" maxlength="35" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">
        </div>
        <div class="mb-3" data-aos="fade-up" data-aos-delay="250">
          <label class="form-label fw-semibold text-success">Email</label>
          <input type="email" class="form-control rounded-pill" 
                 name="email" id="edit-email" required maxlength="35" minlength="4">
        </div>
      </div>
      <div class="modal-footer" data-aos="fade-up" data-aos-delay="300">
        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success rounded-pill px-4">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById('editUserModal');

  modal.addEventListener('show.bs.modal', () => {
    modal.classList.add("animate");
  });

  modal.addEventListener('hidden.bs.modal', () => {
    modal.classList.remove("animate");
  });

  // Existing auto-fill form values
  modal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    document.getElementById('edit-id').value = button.dataset.id;
    document.getElementById('edit-first').value = button.dataset.first;
    document.getElementById('edit-last').value = button.dataset.last;
    document.getElementById('edit-email').value = button.dataset.email;
  });

  // Auto-hide flash messages after 5 seconds
  setTimeout(() => {
    document.querySelectorAll('.alert').forEach(el => {
      let alert = new bootstrap.Alert(el);
      alert.close();
    });
  }, 5000);
});

</script>
<style>
  @keyframes zoomInModal {
  from {
    transform: scale(0.8);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

.modal.animate .modal-dialog {
  animation: zoomInModal 0.4s ease forwards;
}
</style>
<?php require_once "inc/admin-footer.php"; ?>
<?php ob_end_flush(); ?>
