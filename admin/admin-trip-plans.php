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
if ($trip_id <= 0) {
    echo "<div class='alert alert-danger m-3'>Invalid trip ID</div>";
    require_once "inc/admin-footer.php";
    exit;
}

// Trip info
$trip = $conn->query("SELECT * FROM trips WHERE id=$trip_id")->fetch_assoc();
if(!$trip){
    echo "<div class='alert alert-danger m-3'>Trip not found</div>";
    require_once "inc/admin-footer.php";
    exit;
}

/* Delete Plan */
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='delete') {
  require_post_csrf();
  $id = (int)$_POST['id'];
  $row = $conn->query("SELECT image FROM expenses_templates WHERE id=$id AND trip_id=$trip_id")->fetch_assoc();
  if($row && !empty($row['image'])){
      $abs = dirname(__DIR__) . "/" . $row['image'];
      // sirf tab delete karo jab image recommend wali ho (img/images folder me)
      if(strpos($row['image'], "img/images/") === 0 && is_file($abs)){
          @unlink($abs);
      }
  }
  $conn->query("DELETE FROM expenses_templates WHERE id=$id AND trip_id=$trip_id");
  flash('ok','Plan deleted (image removed if recommend).');
  header("Location: admin-trip-plans.php?trip_id=$trip_id");
  exit;
}

/* Add/Update */
if ($_SERVER['REQUEST_METHOD']==='POST' && in_array($_POST['action']??'', ['add','update'])) {
  require_post_csrf();
  $day      = (int)($_POST['day']??1);
  $category = trim($_POST['category']??'');
  $amount   = (float)($_POST['amount']??0);
  $expense_date = trim($_POST['expense_date']??'');
  $notes    = trim($_POST['notes']??'');
  $id       = (int)($_POST['id']??0);

  $errors=[];
  if(!$day) $errors[]="Day required.";
  if(!$category) $errors[]="Category required.";
  if(!$expense_date) {
    $errors[]="Date required.";
  } else {
    if($expense_date < $trip['start_date'] || $expense_date > $trip['end_date']) {
        $errors[]="Date must be between trip start and end dates.";
    }
  }

  // image upload
  $imagePath = null;
  if (!empty($_FILES['image']['name'])) {
      $uploadDir = dirname(__DIR__) . "/img/images/";
      if (!is_dir($uploadDir)) mkdir($uploadDir,0777,true);

      $filename = time()."_".basename($_FILES['image']['name']);
      $target   = $uploadDir.$filename;

      if(move_uploaded_file($_FILES['image']['tmp_name'],$target)){
          $imagePath = "img/images/".$filename;

          // 🟢 Purani image delete sirf update case mein
          if ($_POST['action']==='update' && $id>0) {
              $old = $conn->query("SELECT image FROM expenses_templates WHERE id=$id AND trip_id=$trip_id")->fetch_assoc();
              if($old && !empty($old['image'])){
                  $oldPath = dirname(__DIR__) . "/" . $old['image'];
                  if(is_file($oldPath)) unlink($oldPath);
              }
          }
      }
  }

  if($errors){
      flash('err',implode(' ',$errors),'danger');
  } else {
      if($_POST['action']==='add'){
          $stmt=$conn->prepare("INSERT INTO expenses_templates (trip_id,day,category,amount,expense_date,notes,image) VALUES (?,?,?,?,?,?,?)");
          $stmt->bind_param("iisdsss",$trip_id,$day,$category,$amount,$expense_date,$notes,$imagePath);
          $stmt->execute();
          flash('ok','Plan added.');
      } else {
          if($imagePath){
              $stmt=$conn->prepare("UPDATE expenses_templates SET day=?,category=?,amount=?,expense_date=?,notes=?,image=? WHERE id=? AND trip_id=?");
              $stmt->bind_param("isdsssii",$day,$category,$amount,$expense_date,$notes,$imagePath,$id,$trip_id);
          } else {
              $stmt=$conn->prepare("UPDATE expenses_templates SET day=?,category=?,amount=?,expense_date=?,notes=? WHERE id=? AND trip_id=?");
              $stmt->bind_param("isdssii",$day,$category,$amount,$expense_date,$notes,$id,$trip_id);
          }
          $stmt->execute();
          flash('ok','Plan updated.');
      }
  }
  header("Location: admin-trip-plans.php?trip_id=$trip_id");
  exit;
}

// fetch plans
$res=$conn->query("SELECT * FROM expenses_templates WHERE trip_id=$trip_id ORDER BY day ASC, id ASC");
?>
<style>
/* Show normal table on large screens */
@media (min-width: 768px) {
  .table-responsive-stack tr {
    display: table-row;
  }
}
.table {
  white-space: nowrap !important;
}

</style>

<main class="col-12 col-md-12 col-lg-10 p-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold text-gradient" data-aos="fade-right">
      <i class="bi bi-list-task me-2"></i> Plans for <?=h($trip['trip_name'])?>
    </h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#planModal" data-aos="fade-left">
      <i class="bi bi-plus-lg"></i> Add Plan
    </button>
  </div>

  <div><?= flash('ok') ?: '' ?> <?= flash('err') ?: '' ?></div>

  <div class="card shadow border-0 rounded-3" data-aos="fade-up">
    <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Day</th>
            <th>Category</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Notes</th>
            <th>Image</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if($res->num_rows>0): ?>
          <?php while($row=$res->fetch_assoc()): ?>
            <tr>
              <td><?=$row['id']?></td>
              <td>Day <?=$row['day']?></td>
              <td><?=h($row['category'])?></td>
              <td>$<?=number_format($row['amount'],2)?></td>
              <td><?=h($row['expense_date'])?></td>
              <td><?=h(mb_strimwidth($row['notes'],0,50,"..."))?></td>
              <td>
                <?php if($row['image']): ?>
                  <img src="../<?=h($row['image'])?>" style="width:70px;height:45px;object-fit:cover">
                <?php else: ?>
                  <span class="text-muted">No image</span>
                <?php endif; ?>
              </td>
              <td>
                <button class="btn btn-sm btn-info mx-2" data-bs-toggle="modal" data-bs-target="#planModal"
                  data-edit='<?=json_encode($row)?>'><i class="bi bi-pencil"></i></button>
                <form method="post" class="d-inline" onsubmit="return confirm('Delete this plan?')">
                  <?=csrf_field();?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?=$row['id']?>">
                  <button class="btn btn-sm btn-danger my-2 mx-2"><i class="bi bi-trash"></i></button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="8" class="text-center text-muted">No plans yet.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<!-- Modal -->
<div class="modal fade" id="planModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form method="post" enctype="multipart/form-data" class="modal-content">
      <?=csrf_field();?>
      <input type="hidden" name="id" id="plan_id">
      <input type="hidden" name="action" id="plan_action" value="add">

      <div class="modal-header bg-light">
        <h5 class="modal-title text-dark">Add Plan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-dark">
        <div class="mb-3">
          <label class="form-label">Day</label>
          <input type="number" class="form-control" name="day" id="plan_day" min="1" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Category</label>
          <input type="text" class="form-control" name="category" id="plan_category" required maxlength="50" minlength="4">
        </div>
        <div class="row">
          <div class="col">
            <label class="form-label">Amount</label>
            <input type="number" step="1" class="form-control" name="amount" id="plan_amount" max="999999">
          </div>
          <div class="col">
            <label class="form-label">Date</label>
            <input type="date" class="form-control" 
            name="expense_date" id="plan_date" 
            min="<?=$trip['start_date']?>" 
            max="<?=$trip['end_date']?>" required>
          </div>
        </div>
        <div class="mb-3 mt-3">
          <label class="form-label">Notes</label>
          <textarea class="form-control" name="notes" id="plan_notes" maxlength="250" minlength="5"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Image</label>
          <input type="file" class="form-control" name="image">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
const planModal=document.getElementById('planModal');
planModal.addEventListener('show.bs.modal', e=>{
  const btn=e.relatedTarget;
  const data=btn?.getAttribute('data-edit');
  const form=planModal.querySelector('form');
  if(data){
    const row=JSON.parse(data);
    form.plan_action.value='update';
    form.plan_id.value=row.id;
    form.plan_day.value=row.day;
    form.plan_category.value=row.category;
    form.plan_amount.value=row.amount;
    form.plan_date.value=row.expense_date;
    form.plan_notes.value=row.notes;
    planModal.querySelector('.modal-title').innerText="Edit Plan";
  } else {
    form.reset();
    form.plan_action.value='add';
    form.plan_id.value='';
    planModal.querySelector('.modal-title').innerText="Add Plan";
  }
});

</script>
<script>
  setTimeout(()=>{
    document.querySelectorAll('.alert').forEach(el=>{
      el.classList.add('fade');
      setTimeout(()=> el.remove(), 500); // remove after fade
    });
  }, 5000); // 5 sec
</script>


<?php require_once "inc/admin-footer.php"; ob_end_flush(); ?>
