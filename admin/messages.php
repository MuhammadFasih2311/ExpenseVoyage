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

/* Delete Message */
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '')==='delete') {
  require_post_csrf();
  $id = (int)$_POST['id'];
  $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id=?");
  $stmt->bind_param("i",$id);
  $stmt->execute();
  flash('ok','Message deleted.');
  header("Location: messages.php"); exit;
}

/* Search + Pagination + Sorting */
$q = trim($_GET['q'] ?? '');
$page = (int)($_GET['page'] ?? 1);
$per_page = 10;

$sort = $_GET['sort'] ?? 'id';
$order = $_GET['order'] ?? 'desc';
$allowed = ['id','name','email','subject','created_at'];
if(!in_array($sort,$allowed)) $sort='id';
$order = strtolower($order)==='asc' ? 'ASC' : 'DESC';

$where = " WHERE 1 ";
$params=[]; $types='';
if($q!==''){
  $where .= " AND (
    name LIKE CONCAT('%',?,'%') 
    OR email LIKE CONCAT('%',?,'%') 
    OR subject LIKE CONCAT('%',?,'%') 
    OR message LIKE CONCAT('%',?,'%')
  )";
  $params = [$q,$q,$q,$q]; 
  $types='ssss';
}

$count_sql = "SELECT COUNT(*) c FROM contact_messages".$where;
$stmt = $conn->prepare($count_sql);
if($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = (int)$stmt->get_result()->fetch_assoc()['c'];

list($page,$pages,$offset) = paginate($total,$page,$per_page);

$sql = "SELECT id, name, email, subject, message, created_at 
        FROM contact_messages $where ORDER BY $sort $order LIMIT ?,?";
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
  $url = "messages.php?sort=$col&order=$newOrder".($q?"&q=".urlencode($q):"");
  return "<a href='$url' class='text-decoration-none'>$label $icon</a>";
}
?>

<style>

/* Search box responsiveness */
@media (max-width: 768px) {
  .search-box {
    flex-direction: column;
    text-align: center;
  }
}

/* Table scroll container (same as manage-services) */
.table-responsive {
  overflow-x: auto !important;
  -webkit-overflow-scrolling: touch !important;
}

/* Table should not compress too much */
#messages-table {
  min-width: 850px !important;
}

/* ✅ TABLE HEADINGS ALWAYS SINGLE LINE */
#messages-table th {
  white-space: nowrap !important;
  font-weight: 600 !important;
}

/* ✅ CONTENT (MESSAGE) WILL WRAP NEATLY */
#messages-table td {
  white-space: normal !important;
  word-break: break-word !important;
  vertical-align: middle !important;
}

/* Action column alignment fix */
#messages-table td.text-end {
  white-space: nowrap !important;
}

/* Page title (your Messages heading) aligned nicely on mobile */
@media (max-width: 768px) {
  .page-heading {
    text-align: center !important;
    width: 100%;
  }
}
</style>

<main class="col-12 col-md-12 col-lg-10 p-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-gradient mb-0 page-heading" data-aos="fade-right">
      <i class="bi bi-envelope me-2" ></i> Messages
    </h2>
  </div>

  <form class="search-box d-flex align-items-center gap-2 p-2 rounded-4 shadow-sm bg-light my-3"
  method="get" data-aos="fade-left">
  <div class="input-group input-group-sm flex-grow-1">
    <span class="input-group-text bg-white border-0 rounded-pill ps-3">
      <i class="bi bi-search text-muted"></i>
    </span>
    <input type="text" 
           class="form-control form-control-sm border-0 rounded-pill" 
           placeholder="Search name / email / subject / message" 
           name="q" 
           value="<?= htmlspecialchars($q) ?>" maxlength="50">
  </div>

  <button class="btn btn-sm btn-primary rounded-pill px-3">
    <i class="bi bi-search"></i> Search
  </button>

  <?php if($q): ?>
    <a href="messages.php" 
       class="btn btn-sm btn-outline-danger rounded-pill px-3" 
       data-aos="fade-left">
      <i class="bi bi-x-circle"></i> Reset
    </a>
  <?php endif; ?>
</form>


  <div>
    <?= flash('ok') ?: '' ?>
    <?= flash('err') ?: '' ?>
  </div>

  <div class="card shadow-lg border-0 rounded-4 overflow-hidden" data-aos="fade-up">
    <div class="table-responsive" style="overflow-x:auto; -webkit-overflow-scrolling:touch;">
     <table id="messages-table" class="table table-hover table-striped align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th><?= sort_link('id','ID',$sort,$order,$q) ?></th>
            <th><?= sort_link('name','Name',$sort,$order,$q) ?></th>
            <th><?= sort_link('email','Email',$sort,$order,$q) ?></th>
            <th><?= sort_link('subject','Subject',$sort,$order,$q) ?></th>
            <th>Message</th>
            <th><?= sort_link('created_at','Date',$sort,$order,$q) ?></th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while($m = $res->fetch_assoc()): ?>
            <tr>
              <td><?= $m['id'] ?></td>
              <td><?= htmlspecialchars($m['name']) ?></td>
              <td><?= htmlspecialchars($m['email']) ?></td>
              <td><?= htmlspecialchars($m['subject']) ?></td>
              <td style="max-width:200px; white-space:normal; word-break:break-word;">
                <?= htmlspecialchars(mb_strimwidth($m['message'],0,60,"...")) ?>
                <?php if(strlen($m['message']) > 60): ?>
                  <button type="button" 
                          class="btn btn-sm btn-outline-info rounded-pill ms-2"
                          data-bs-toggle="modal" 
                          data-bs-target="#viewMsgModal"
                          data-id="<?= $m['id'] ?>"
                          data-name="<?= htmlspecialchars($m['name']) ?>"
                          data-email="<?= htmlspecialchars($m['email']) ?>"
                          data-subject="<?= htmlspecialchars($m['subject']) ?>"
                          data-message="<?= htmlspecialchars($m['message']) ?>"
                          data-date="<?= date("M d, Y H:i", strtotime($m['created_at'])) ?>">
                    View
                  </button>
                <?php endif; ?>
              </td>
              <td><span class="badge bg-light text-dark"><?= date("M d, Y", strtotime($m['created_at'])) ?></span></td>
              <td class="text-end">
                <form method="post" class="d-inline" 
                      onsubmit="return confirm('Delete this message?');">
                  <?= csrf_field(); ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $m['id'] ?>">
                  <button class="btn btn-sm btn-danger rounded-circle" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
                <!-- Direct reply button -->
                <a href="mailto:<?= htmlspecialchars($m['email']) ?>?subject=Re: <?= urlencode($m['subject']) ?>" 
                   class="btn btn-sm btn-success rounded-circle my-3" title="Reply via Email">
                  <i class="bi bi-reply-fill"></i>
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pagination -->
  <?php if($pages > 1): ?>
    <nav class="mt-4" aria-label="Messages pagination">
      <ul class="pagination justify-content-center">
        <?php if($page > 1): ?>
          <li class="page-item"><a class="page-link" href="?page=<?= $page-1 ?>&q=<?=urlencode($q)?>&sort=<?=$sort?>&order=<?=$order?>">&laquo; Prev</a></li>
        <?php endif; ?>

        <?php for($i=1;$i<=$pages;$i++): ?>
          <li class="page-item <?=($i==$page?'active':'')?>"><a class="page-link" href="?page=<?=$i?>&q=<?=urlencode($q)?>&sort=<?=$sort?>&order=<?=$order?>"><?=$i?></a></li>
        <?php endfor; ?>

        <?php if($page < $pages): ?>
          <li class="page-item"><a class="page-link" href="?page=<?= $page+1 ?>&q=<?=urlencode($q)?>&sort=<?=$sort?>&order=<?=$order?>">Next &raquo;</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  <?php endif; ?>
</main>

<!-- View Message Modal -->
<div class="modal fade text-dark" id="viewMsgModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold text-dark">
          <i class="bi bi-envelope-open me-2"></i> View Message
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p><strong>Name:</strong> <span id="msg-name"></span></p>
        <p><strong>Email:</strong> <span id="msg-email"></span></p>
        <p><strong>Subject:</strong> <span id="msg-subject"></span></p>
        <p><strong>Date:</strong> <span id="msg-date"></span></p>
        <hr>
        <p id="msg-message" class="text-dark"></p>
      </div>
      <div class="modal-footer">
        <a id="replyBtn" class="btn btn-success rounded-pill" target="_blank">
          <i class="bi bi-reply-fill"></i> Reply via Email
        </a>
        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const viewModal = document.getElementById('viewMsgModal');
  viewModal.addEventListener('show.bs.modal', event => {
    const btn = event.relatedTarget;
    document.getElementById('msg-name').innerText = btn.getAttribute('data-name');
    document.getElementById('msg-email').innerText = btn.getAttribute('data-email');
    document.getElementById('msg-subject').innerText = btn.getAttribute('data-subject');
    document.getElementById('msg-message').innerText = btn.getAttribute('data-message');
    document.getElementById('msg-date').innerText = btn.getAttribute('data-date');

    const replyBtn = document.getElementById('replyBtn');
    const email = btn.getAttribute('data-email');
    const subject = "Re: " + btn.getAttribute('data-subject');
    replyBtn.href = "mailto:" + email + "?subject=" + encodeURIComponent(subject);
  });
});
</script>

<?php require_once "inc/admin-footer.php"; ?>
<?php ob_end_flush(); ?>
