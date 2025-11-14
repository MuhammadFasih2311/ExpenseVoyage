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

/* ---------------- Delete Blog ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
  require_post_csrf();
  $id = (int)$_POST['id'];

  // delete main image
  $stmt = $conn->prepare("SELECT image FROM blogs WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($row = $res->fetch_assoc()) {
    if (!empty($row['image'])) {
      $path = dirname(__DIR__) . "/" . $row['image'];
      if (file_exists($path)) unlink($path);
    }
  }

  // delete gallery images
  $res2 = $conn->query("SELECT gallery_images FROM blog_details WHERE blog_id=$id");
  while ($g = $res2->fetch_assoc()) {
    if (!empty($g['gallery_images'])) {
      foreach (explode(",", $g['gallery_images']) as $img) {
        $img = trim($img);
        if ($img && file_exists("../" . $img)) unlink("../" . $img);
      }
    }
  }

  // delete blog details + main row
  $conn->query("DELETE FROM blog_details WHERE blog_id=$id");
  $stmt = $conn->prepare("DELETE FROM blogs WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();

  flash('ok', 'Blog deleted.');
  header("Location: admin-blogs.php");
  exit;
}

/* ---------------- Add / Update Blog ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($_POST['action'] ?? '', ['add', 'update'])) {
  require_post_csrf();

  $id          = (int)($_POST['id'] ?? 0);
  $title       = trim($_POST['title'] ?? '');
  $short_desc  = trim($_POST['short_desc'] ?? '');
  $category    = trim($_POST['category'] ?? '');
  $author      = trim($_POST['author'] ?? '');
  $date        = trim($_POST['date'] ?? '');
  $tags        = trim($_POST['tags'] ?? '');
  $reading_time= trim($_POST['reading_time'] ?? '');
  $location    = trim($_POST['location'] ?? '');
  $rating      = (float)($_POST['rating'] ?? 0);

  $errors = [];
  if (!$title) $errors[] = "Title required.";
  if (!$category) $errors[] = "Category required.";
  if (!$author) $errors[] = "Author required.";

  // image upload
  $imagePath = null;
  if (!empty($_FILES['image']['name'])) {
    $uploadDir = dirname(__DIR__) . "/img/blogs/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = time() . "_" . uniqid() . "." . $ext;
    $target = $uploadDir . $filename;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
      $imagePath = "img/blogs/" . $filename;

      // delete old image if update
      if ($id) {
        $old = $conn->query("SELECT image FROM blogs WHERE id=$id")->fetch_assoc();
        if ($old && $old['image'] && file_exists("../" . $old['image'])) {
          @unlink("../" . $old['image']);
        }
      }
    }
  }

  if ($errors) {
    flash('err', implode(' ', $errors), 'danger');
    header("Location: admin-blogs.php");
    exit;
  }

  if ($_POST['action'] === 'add') {
    $stmt = $conn->prepare("INSERT INTO blogs (title, short_desc, category, author, date, tags, reading_time, location, rating, image)
                            VALUES (?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssssds", $title, $short_desc, $category, $author, $date, $tags, $reading_time, $location, $rating, $imagePath);
    $stmt->execute();
    flash('ok', 'Blog added.');
  } else {
    if ($imagePath) {
      $stmt = $conn->prepare("UPDATE blogs SET title=?, short_desc=?, category=?, author=?, date=?, tags=?, reading_time=?, location=?, rating=?, image=? WHERE id=?");
      $stmt->bind_param("ssssssssdsi", $title, $short_desc, $category, $author, $date, $tags, $reading_time, $location, $rating, $imagePath, $id);
    } else {
      $stmt = $conn->prepare("UPDATE blogs SET title=?, short_desc=?, category=?, author=?, date=?, tags=?, reading_time=?, location=?, rating=? WHERE id=?");
      $stmt->bind_param("ssssssssdi", $title, $short_desc, $category, $author, $date, $tags, $reading_time, $location, $rating, $id);
    }
    $stmt->execute();
    flash('ok', 'Blog updated.');
  }

  header("Location: admin-blogs.php");
  exit;
}

/* ---------------- Search + Pagination ---------------- */
$q = trim($_GET['q'] ?? '');
$where = "";
if ($q !== '') {
  $qLike = "%" . $conn->real_escape_string($q) . "%";
  $where = "WHERE title LIKE '$qLike' OR category LIKE '$qLike' OR author LIKE '$qLike' OR tags LIKE '$qLike'";
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$totalRes = $conn->query("SELECT COUNT(*) AS cnt FROM blogs $where");
$totalRows = $totalRes->fetch_assoc()['cnt'];
$totalPages = ceil($totalRows / $limit);

$sql = "SELECT * FROM blogs $where ORDER BY id DESC LIMIT $limit OFFSET $offset";
$res = $conn->query($sql);
?>

<style>
.table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.table-responsive table { min-width: 950px; }
.table th, .table td { vertical-align: middle !important; white-space: nowrap; }
.table td img { width: 70px; height: 45px; object-fit: cover; border-radius: 6px; }
@media(max-width:768px){
  form.d-flex{flex-direction:column;gap:10px;}
}
</style>

<main class="col-12 col-md-12 col-lg-10 p-4">

  <!-- Heading + Add -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold text-gradient" data-aos="fade-right"><i class="bi bi-journal-text me-2"></i> Manage Blogs</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#blogModal" data-aos="fade-left">
      <i class="bi bi-plus-lg"></i> Add Blog
    </button>
  </div>

  <!-- Search -->
  <form class="d-flex align-items-center gap-2 p-2 rounded-4 shadow-sm bg-light my-3" method="get" data-aos="fade-left">
    <div class="input-group input-group-sm flex-grow-1">
      <span class="input-group-text bg-white border-0 rounded-pill ps-3">
        <i class="bi bi-search text-muted"></i>
      </span>
      <input type="text" class="form-control form-control-sm border-0 rounded-pill"
             placeholder="Search title / category / author / tags"
             name="q" value="<?= htmlspecialchars($q) ?>" maxlength="50">
    </div>
    <button class="btn btn-sm btn-primary rounded-pill px-3"><i class="bi bi-search"></i> Search</button>
    <?php if ($q): ?>
      <a href="admin-blogs.php" class="btn btn-sm btn-outline-danger rounded-pill px-3">
        <i class="bi bi-x-circle"></i> Reset
      </a>
    <?php endif; ?>
  </form>

  <!-- Alerts -->
  <div><?= flash('ok') ?: '' ?><?= flash('err') ?: '' ?></div>

  <!-- Table -->
  <div class="card shadow border-0 rounded-3 mt-3" data-aos="fade-up">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Title</th>
            <th>Category</th>
            <th>Author</th>
            <th>Date</th>
            <th>Rating</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($res->num_rows > 0): ?>
          <?php while ($b = $res->fetch_assoc()): ?>
            <tr>
              <td><?= $b['id'] ?></td>
              <td><?php if ($b['image']): ?><img src="../<?= h($b['image']) ?>"><?php endif; ?></td>
              <td><?= h($b['title']) ?></td>
              <td><?= h($b['category']) ?></td>
              <td><?= h($b['author']) ?></td>
              <td><?= h($b['date']) ?></td>
              <td><?= h($b['rating']) ?></td>
              <td>
                <a href="admin-blog-detail.php?blog_id=<?= $b['id'] ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                <button class="btn btn-sm btn-warning" 
              data-bs-toggle="modal" 
              data-bs-target="#blogModal"
              data-edit='<?= htmlspecialchars(json_encode($b), ENT_QUOTES, "UTF-8") ?>'>
              <i class="bi bi-pencil"></i>
            </button>
                <form method="post" class="d-inline" onsubmit="return confirm('Delete this blog?');">
                  <?= csrf_field(); ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $b['id'] ?>">
                  <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="8" class="text-center text-muted">No blogs found.</td></tr>
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
        <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&q=<?= urlencode($q) ?>">&laquo; Prev</a></li>
      <?php endif; ?>
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= ($i == $page ? 'active' : '') ?>">
          <a class="page-link" href="?page=<?= $i ?>&q=<?= urlencode($q) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      <?php if ($page < $totalPages): ?>
        <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&q=<?= urlencode($q) ?>">Next &raquo;</a></li>
      <?php endif; ?>
    </ul>
  </nav>
  <?php endif; ?>
</main>

<!-- Modal -->
<div class="modal fade text-dark" id="blogModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form method="post" enctype="multipart/form-data" class="modal-content">
      <?= csrf_field(); ?>
      <input type="hidden" name="id" id="blog_id">
      <input type="hidden" name="action" id="blog_action" value="add">
      <div class="modal-header bg-light">
        <h5 class="modal-title">Add Blog</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2"><label>Title</label><input type="text" class="form-control" name="title" id="blog_title" required maxlength="80"></div>
        <div class="mb-2"><label>Short Desc</label><textarea class="form-control" name="short_desc" id="blog_short_desc" maxlength="120"></textarea></div>
        <div class="mb-2"><label>Category</label><input type="text" class="form-control" name="category" id="blog_category" maxlength="60"></div>
        <div class="row">
          <div class="col"><label>Author</label><input type="text" class="form-control" name="author" id="blog_author" maxlength="40"></div>
          <div class="col"><label>Date</label><input type="date" class="form-control" name="date" id="blog_date"></div>
        </div>
        <div class="row mt-2">
          <div class="col"><label>Tags</label><input type="text" class="form-control" name="tags" id="blog_tags" maxlength="40"></div>
          <div class="col"><label>Reading Time</label><input type="text" class="form-control" name="reading_time" id="blog_reading_time" maxlength="30"></div>
        </div>
        <div class="mt-2"><label>Location</label><input type="text" class="form-control" name="location" id="blog_location" maxlength="50"></div>
        <div class="mb-2"><label>Rating</label><input type="number" step="0.1" class="form-control" name="rating" id="blog_rating" maxlength="30"></div>
        <div class="mb-2"><label>Image</label><input type="file" class="form-control" name="image"></div>
      </div>
      <div class="modal-footer"><button type="submit" class="btn btn-success">Save</button></div>
    </form>
  </div>
</div>

<script>
const blogModal = document.getElementById('blogModal');
blogModal.addEventListener('show.bs.modal', e => {
  const btn = e.relatedTarget;
  const form = blogModal.querySelector('form');

  // reset by default
  form.reset();
  form.blog_action.value = 'add';
  form.blog_id.value = '';
  blogModal.querySelector('.modal-title').innerText = "Add Blog";

  if (!btn) return; // safety

  const dataAttr = btn.getAttribute('data-edit');
  console.log('data-edit attribute:', dataAttr); // debug

  if (dataAttr) {
    try {
      const row = JSON.parse(dataAttr);
      form.blog_action.value = 'update';
      form.blog_id.value = row.id ?? '';
      form.blog_title.value = row.title ?? '';
      form.blog_short_desc.value = row.short_desc ?? '';
      form.blog_category.value = row.category ?? '';
      form.blog_author.value = row.author ?? '';
      form.blog_date.value = row.date ?? '';
      form.blog_tags.value = row.tags ?? '';
      form.blog_reading_time.value = row.reading_time ?? '';
      form.blog_location.value = row.location ?? '';
      form.blog_rating.value = row.rating ?? '';
      blogModal.querySelector('.modal-title').innerText = "Edit Blog";
    } catch (err) {
      console.error('Failed to parse data-edit JSON', err);
      // leave in "add" mode
    }
  }
});

// auto hide alerts
setTimeout(()=>{document.querySelectorAll('.alert').forEach(a=>a.remove())},5000);
</script>

<?php
require_once "inc/admin-footer.php";
ob_end_flush();
?>
