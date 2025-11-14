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

$blog_id = (int)($_GET['blog_id'] ?? 0);
if(!$blog_id){ header("Location: admin-blogs.php"); exit; }

/* Fetch blog */
$stmt = $conn->prepare("SELECT * FROM blogs WHERE id=?");
$stmt->bind_param("i",$blog_id);
$stmt->execute();
$blog = $stmt->get_result()->fetch_assoc();
if(!$blog){ echo "<div class='p-5 text-danger'>Blog not found.</div>"; exit; }

/* Get blog details */
$res = $conn->query("SELECT * FROM blog_details WHERE blog_id=$blog_id");
$detail = $res->fetch_assoc();

/* Add empty details if not exist */
if(!$detail){
  $conn->query("INSERT INTO blog_details (blog_id,content,gallery_images) VALUES ($blog_id,'','')");
  $detail = $conn->query("SELECT * FROM blog_details WHERE blog_id=$blog_id")->fetch_assoc();
}

/* ===== Update Content Only ===== */
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '')==='update_content') {
  require_post_csrf();
  $content = trim($_POST['content'] ?? '');
  $stmt=$conn->prepare("UPDATE blog_details SET content=? WHERE blog_id=?");
  $stmt->bind_param("si",$content,$blog_id);
  $stmt->execute();
  flash('ok','Content updated.');
  header("Location: admin-blog-detail.php?blog_id=".$blog_id); exit;
}

/* ===== Update Gallery Only ===== */
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '')==='update_gallery') {
  require_post_csrf();

  $old = $detail['gallery_images'] ? explode(",",$detail['gallery_images']) : [];
  $gallery_arr = [ $old[0] ?? '', $old[1] ?? '', $old[2] ?? '' ];

  // delete marks
  if (!empty($_POST['delete_old'])) {
    foreach($_POST['delete_old'] as $delImg){
      foreach($gallery_arr as $k=>$v){
        if($v==$delImg){
          if(file_exists(__DIR__."/../".$v)) @unlink(__DIR__."/../".$v);
          $gallery_arr[$k] = '';
        }
      }
    }
  }

  // upload new slot-wise
  $uploadDir = __DIR__."/../img/blogs/gallery/";
  if(!is_dir($uploadDir)) mkdir($uploadDir,0777,true);

  $newFiles = [];
  for($i=1;$i<=3;$i++){
    if(!empty($_FILES["gallery$i"]['name'])){
      $name=$_FILES["gallery$i"]['name'];
      $ext = pathinfo($name, PATHINFO_EXTENSION);
      $filename=time()."_".rand(1000,9999).".".$ext;
      $target=$uploadDir.$filename;
      if(move_uploaded_file($_FILES["gallery$i"]['tmp_name'],$target)){
        $gallery_arr[$i-1] = "img/blogs/gallery/".$filename;
        $newFiles[] = "img/blogs/gallery/".$filename;
      }
    }
  }

    // filter empty
  $final = array_values(array_filter($gallery_arr));

  // validate only max (0–3 allowed)
  if(count($final) > 3){
      foreach($newFiles as $nf){
        if(file_exists(__DIR__."/../".$nf)) unlink(__DIR__."/../".$nf);
      }
      flash('err','Maximum 3 gallery images allowed.');
      header("Location: admin-blog-detail.php?blog_id=".$blog_id); exit;
  }

  // save to DB (0 bhi ho sakti hai)
  $gallery_str = implode(",",$final);
  $stmt=$conn->prepare("UPDATE blog_details SET gallery_images=? WHERE blog_id=?");
  $stmt->bind_param("si",$gallery_str,$blog_id);
  $stmt->execute();
  flash('ok','Gallery updated.');
  header("Location: admin-blog-detail.php?blog_id=".$blog_id); exit;
}
?>

<main class="col-12 col-md-12 col-lg-10 p-4">
  <h2 class="fw-bold text-gradient mb-4" data-aos="fade-right">
    <i class="bi bi-journal-text me-2"></i> Blog Details - <?=h($blog['title'])?>
  </h2>

  <div><?= flash('ok') ?: '' ?><?= flash('err') ?: '' ?></div>

  <!-- Blog Basic Info -->
  <div class="card shadow mb-4 p-3" data-aos="zoom-in">
    <div class="row">
      <div class="col-md-3">
        <?php if($blog['image']): ?>
          <img src="../<?=$blog['image']?>" class="img-fluid rounded">
        <?php endif; ?>
      </div>
      <div class="col-md-9">
        <h4><?=h($blog['title'])?></h4>
        <p class="text-muted"><?=h($blog['short_desc'])?></p>
      </div>
    </div>
  </div>

  <!-- Content -->
  <div class="card shadow p-3 mb-4" data-aos="fade-up">
    <h4>Edit Content</h4>
    <form method="post">
      <?=csrf_field();?>
      <input type="hidden" name="action" value="update_content">
      <textarea class="form-control" name="content" rows="6" maxlength="2000"><?=h($detail['content'])?></textarea>
      <button class="btn btn-success mt-3">Save Content</button>
    </form>
  </div>

  <!-- Gallery -->
  <div class="card shadow p-3">
    <h4>Gallery Images</h4>
    <form method="post" enctype="multipart/form-data" id="galleryForm">
      <?=csrf_field();?>
      <input type="hidden" name="action" value="update_gallery">

      <div class="row">
        <?php 
          $imgs = $detail['gallery_images'] ? explode(",",$detail['gallery_images']) : [];
          for($i=1;$i<=3;$i++): $img = $imgs[$i-1] ?? ''; ?>
          <div class="col-md-4 mb-3 slot" data-slot="<?=$i?>">
            <?php if($img): ?>
              <div class="position-relative">
                <img src="../<?=$img?>" class="img-thumbnail w-100" style="max-height:160px;object-fit:cover;">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" data-delete>✕</button>
                <input type="checkbox" name="delete_old[]" value="<?=$img?>" class="d-none">
              </div>
            <?php else: ?>
              <input type="file" name="gallery<?=$i?>" class="form-control" accept="image/*">
            <?php endif; ?>
          </div>
        <?php endfor; ?>
      </div>
      
      <button class="btn btn-primary mt-2" type="submit" disabled>Update Gallery</button>
    </form>
  </div>
</main>

<script>
function updateGalleryBtn(){
  const form = document.getElementById("galleryForm");
  const updateBtn = form.querySelector("button[type='submit']");
  const hasDelete = form.querySelectorAll("input[name='delete_old[]']:checked").length > 0;
  const hasFile = Array.from(form.querySelectorAll("input[type='file']")).some(inp=>inp.value);
  updateBtn.disabled = !(hasDelete || hasFile);
}

document.addEventListener("DOMContentLoaded",()=>{
  updateGalleryBtn();

  // file change
  document.querySelectorAll("#galleryForm input[type='file']").forEach(inp=>{
    inp.addEventListener("change", updateGalleryBtn);
  });

  // delete click
  document.querySelectorAll("#galleryForm button[data-delete]").forEach(btn=>{
    btn.addEventListener("click", ()=>{
      if(confirm("Delete this image?")){
        const slot = btn.closest(".slot");
        const chk = slot.querySelector("input[type='checkbox']");
        chk.checked = true;
        slot.querySelector("img").style.opacity=0.5;
        btn.disabled = true;
        updateGalleryBtn();
      }
    });
  });
});
document.addEventListener("DOMContentLoaded", () => {
  const alerts = document.querySelectorAll(".alert, .text-danger, .text-success");
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.style.transition = "opacity 0.5s ease";
      alert.style.opacity = "0";
      setTimeout(() => alert.remove(), 500); // DOM se remove bhi kar dega
    }, 5000); // 5 sec
  });
});
document.addEventListener("DOMContentLoaded", function() {
  if (typeof AOS !== "undefined") {
    AOS.init({
      once: true, // ek hi baar chale
      duration: 800,
    });
  }
});
</script>

<?php require_once "inc/admin-footer.php"; ?>
<?php ob_end_flush(); ?>
