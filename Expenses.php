<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Track and manage all your trip expenses with Expense Voyage. Keep your travel budget organized and enjoy a stress-free journey.">
<title>Trip Expenses - Expense Voyage</title>
  <link rel="icon" href="images/logo.png" type="image/png">
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<style>
  html, body {
  overflow-x: hidden;
}
#liveToast {
  transform: translateX(100%);
  opacity: 0;
  transition: all 0.4s ease-in-out;
}
#liveToast.show {
  transform: translateX(0);
  opacity: 1;
}
/* Custom zoom-in modal animation */
.modal.fade .modal-dialog {
  transform: scale(0.8);
  opacity: 0;
  transition: all 0.3s ease-in-out;
}
.modal.fade.show .modal-dialog {
  transform: scale(1);
  opacity: 1;
}
</style>
<body>
<?php
include 'connect.php';
include 'header.php';
?>
<!-- Hero Section -->
<section class="page-hero d-flex align-items-center justify-content-center text-center position-relative"
  style="background-image: url('images/expenses.jpg'); background-attachment: fixed; background-size: cover; background-position: center; height: 350px;">
  <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.5);"></div>
  <div class="container position-relative text-white" data-aos="zoom-in">
    <h1 class="fw-bold">Travel Expenses</h1>
    <p class="lead">Plan smarter with detailed budget tracking</p>
  </div>
</section>

<div class="container py-5">
  <div class="text-center mb-5">
    <h2 class="fw-bold section-title" data-aos="fade-up" data-aos-delay="100">Manage Your Trip Costs</h2>
    <p class="lead" data-aos="fade-up" data-aos-delay="200">Easily record, organize and track your travel spending in one place</p>
  </div>

  <!-- Trip Selector -->
  <form id="tripForm" class="mb-4">
    <div class="row g-2 align-items-end">
      <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
        <label class="form-label">Select Trip</label>
        <select name="trip_id" class="form-select" required>
          <option value="">-- Choose Trip --</option>
          <?php 
          $trips_result = mysqli_query($conn, "SELECT id, trip_name FROM trips ORDER BY trip_name ASC");
          while ($trip = mysqli_fetch_assoc($trips_result)) { ?>
            <option value="<?= $trip['id'] ?>">
              <?= htmlspecialchars($trip['trip_name']); ?>
            </option>
          <?php } ?>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-gradient w-100" data-aos="fade-up" data-aos-delay="100">View</button>
      </div>
    </div>
  </form>
       
  <!-- Search trips -->
<div class="row g-2 mb-4" data-aos="fade-up" data-aos-delay="100">
  <div class="col-md-6">
    <input type="text" id="searchTrips" class="form-control" placeholder="Search trips..." maxlength="30">
  </div>
</div>

<!-- Trips list -->
<div class="row g-4" id="tripsList"></div>


  <!-- Trip header (image + name) -->
  <div id="trip-header" class="my-4" data-aos="zoom-in" data-aos-delay="200"></div>

  <!-- Recommended Plan -->
  <button id="useTemplateBtn" class="btn btn-gradient mb-3 d-none" data-aos="fade-right" data-aos-delay="200">Use Recommended Plan</button>

  <!-- Add Expense -->
  <div class="card mb-4 d-none" id="addExpenseCard" data-aos="zoom-in" data-aos-delay="100">
    <div class="card-body">
      <h5 class="mb-3">Add New Expense</h5>
      <form class="row g-3 add-expense-form" enctype="multipart/form-data">
        <input type="hidden" name="trip_id" id="trip_id_hidden">

        <div class="col-md-2">
          <input type="text" name="category" class="form-control" placeholder="Category" required minlength="3" maxlength="30" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">
        </div>
        <div class="col-md-2">
          <input type="number" step="1" name="amount" class="form-control" placeholder="Amount" required min="1" max="999999">
        </div>
        <div class="col-md-2">
          <input type="date" name="expense_date" class="form-control" required id="expense-date">
        </div>
        <div class="col-md-2">
          <input type="text" name="notes" class="form-control" placeholder="Notes (optional)" minlength="5" maxlength="150">
        </div>
        <div class="col-md-2">
          <input type="file" name="image" class="form-control" accept="image/*">
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-success w-100">Add</button>
        </div>
      </form>
    </div>
  </div>
            
  <!-- Expenses Table -->
  <div id="expenses-table-container" data-aos="flip-up" data-aos-delay="500"></div>

  <!-- Book Trip Button -->
<div class="text-center my-5" data-aos="zoom-in" data-aos-delay="200">
  <?php 
    if (isset($_GET['trip_id']) && is_numeric($_GET['trip_id'])) { 
        $trip_id = intval($_GET['trip_id']); 
  ?>
      <a href="book-trip.php?trip_id=<?= $trip_id; ?>" class="btn btn-gradient btn-lg">
        <i class="bi bi-calendar-check"></i> Book This Trip
      </a>
  <?php } ?>
</div>

  <!-- Edit Modal -->
  <div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Expense</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form enctype="multipart/form-data">
            <input type="hidden" name="exp_id" id="edit_id">
            <div class="mb-3">
              <label class="form-label">Category</label>
              <input type="text" name="category" id="edit_category" class="form-control" required minlength="3" maxlength="30">
            </div>
                    <div class="mb-3">
          <label class="form-label">Amount</label>
          <input type="number" step="1" name="amount" id="edit_amount" class="form-control" required min="1" max="999999">
        </div>

        <div class="mb-3">
          <label class="form-label">Date</label>
          <input type="date" name="expense_date" id="edit_date" class="form-control" required>
        </div>
            <div class="mb-3">
              <label class="form-label">Notes</label>
              <input type="text" name="notes" id="edit_notes" class="form-control" minlength="4" maxlength="120">
            </div>
            <div class="mb-3">
              <label class="form-label">Image</label>
              <input type="file" name="image" id="edit_image" class="form-control" accept="image/*">
              <div class="mt-2" id="edit_preview"></div>
            </div>
            <button type="submit" class="btn btn-success">Update</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
       
<!-- Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
  <div id="liveToast" class="toast align-items-center text-white border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body" id="toastMsg"></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<?php
$related_sql = "SELECT * FROM trips ORDER BY RAND() LIMIT 3";
$related_trips = mysqli_query($conn, $related_sql);
?>

<?php if ($related_trips && mysqli_num_rows($related_trips) > 0): ?>
<section class="py-5 bg-light">
  <div class="container">
    <h3 class="mb-4 fw-bold" data-aos="fade-up">Related Trips</h3>
    <div class="row g-4">
      <?php while ($rel = mysqli_fetch_assoc($related_trips)): ?>
        <?php 
          $imagePath = !empty($rel['image']) ? htmlspecialchars($rel['image']) : 'img/trip-placeholder.jpg';
          $shortDesc = strlen($rel['description']) > 80 
                      ? substr($rel['description'], 0, 80) . '...' 
                      : $rel['description'];
        ?>
        <div class="col-md-4 col-sm-12" data-aos="zoom-in">
          <div class="trip-card shadow-lg h-100 border-0 rounded-3 overflow-hidden">
            <div class="position-relative">
              <img src="<?= $imagePath; ?>" 
                   alt="<?= htmlspecialchars($rel['trip_name']); ?>" 
                   style="width:100%; height:250px; object-fit:cover;">
              <span class="badge bg-primary position-absolute top-0 end-0 m-2">
                $<?= number_format($rel['budget'], 2); ?>
              </span>
            </div>
            <div class="trip-info p-3">
              <h5><?= htmlspecialchars($rel['trip_name']); ?></h5>
              <p class="text-muted mb-1"><?= htmlspecialchars($rel['destination']); ?></p>
              <p class="small text-muted mb-2">
                <?= date("M d, Y", strtotime($rel['start_date'])); ?> - 
                <?= date("M d, Y", strtotime($rel['end_date'])); ?>
              </p>
              <p class="trip-desc"><?= htmlspecialchars($shortDesc); ?></p>
              <a href="trip-details.php?id=<?= $rel['id']; ?>" class="btn btn-gradient w-100 mt-2">
                View Details
              </a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include 'footer.php'; ?>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 800, // animation ka time (ms) slow aur smooth ke liye
    easing: 'ease-in-out', // smooth easing
    once: false, // baar-baar animation chalay
  });
</script>
<script>
// Search trips
document.getElementById("searchTrips")?.addEventListener("keyup", function(){
  let val = this.value.trim();

  if(val === "") {
    // trips cards clear karo
    document.getElementById("tripsList").innerHTML = "";

    // ✅ expenses wapas reload karo
    let trip_id = document.getElementById("trip_id_hidden").value;
    if(trip_id){
      loadExpenses(trip_id);
    }
    return;
  }

  // warna trips cards fetch karo
  fetch("expenses_fetch.php?search="+encodeURIComponent(val))
    .then(r=>r.text())
    .then(html=>{
      document.getElementById("tripsList").innerHTML = html;
      AOS.refreshHard();
    });
});

// Toast function
function showToast(message, type="success"){
  let toastEl = document.getElementById("liveToast");
  let toastMsg = document.getElementById("toastMsg");
  toastMsg.innerText = message;
  toastEl.classList.remove("bg-success","bg-danger","bg-warning");
  if(type==="success") toastEl.classList.add("bg-success");
  else if(type==="danger") toastEl.classList.add("bg-danger");
  else toastEl.classList.add("bg-warning");
  new bootstrap.Toast(toastEl).show();
}

/* ========= helpers ========= */
function setUrlTrip(id){
  const url = new URL(window.location.href);
  url.searchParams.set('trip_id', id);
  history.replaceState({}, '', url);
}
function getUrlTrip(){
  return new URLSearchParams(location.search).get('trip_id');
}

/* ========= Trip Selector AJAX ========= */
document.getElementById("tripForm")?.addEventListener("submit", function(e){
  e.preventDefault();
  const trip_id = this.trip_id.value;
  if(!trip_id) return;

  // save + URL update (taa-ke reload pe yahi trip aaye)
  localStorage.setItem("selectedTrip", trip_id);
  setUrlTrip(trip_id);

  // trip header load
  fetch("expenses_table.php?trip_header=1&trip_id="+trip_id)
    .then(r=>r.text())
    .then(html=>{ 
      document.querySelector("#trip-header").innerHTML = html; 
      AOS.refreshHard();
    });

  // expenses table load
  fetch("expenses_table.php?trip_id="+trip_id)
    .then(r=>r.text())
    .then(html=>{ 
      document.querySelector("#expenses-table-container").innerHTML = html; 
      document.getElementById("trip_id_hidden").value = trip_id;
      document.getElementById("addExpenseCard").classList.remove("d-none");
      document.getElementById("useTemplateBtn").classList.remove("d-none");
      document.getElementById("useTemplateBtn").setAttribute("onclick","useTemplate("+trip_id+")");
      AOS.refreshHard();
    });
});

/* ========= On load: URL > localStorage fallback ========= */
window.addEventListener("load", function(){
  const selectEl = document.querySelector("#tripForm select");
  const formEl   = document.querySelector("#tripForm");
  if(!selectEl || !formEl) return;

  const idFromUrl = getUrlTrip();
  const lastTrip  = localStorage.getItem("selectedTrip");

  if(idFromUrl){
    // view-details se aaye ho → URL wali trip dikhao + store bhi karo
    localStorage.setItem("selectedTrip", idFromUrl);
    selectEl.value = idFromUrl;
    formEl.dispatchEvent(new Event("submit"));
  } else if(lastTrip){
    // URL nahi hai → last selected (dropdown) dikhao + URL me bhi set karo
    selectEl.value = lastTrip;
    setUrlTrip(lastTrip);
    formEl.dispatchEvent(new Event("submit"));
  }
});

/* ========= Template ========= */
function useTemplate(trip_id){
  if(!trip_id) return;
  if(!confirm("Are you sure you want to apply the recommended plan?")) return;
  let fd = new FormData();
  fd.append("action","use_template");
  fd.append("trip_id", trip_id);
  fetch("ajax_expenses.php",{ method:"POST", body:fd })
  .then(r=>r.json()).then(d=>{
    if(d.success){ loadExpenses(trip_id); showToast("✅ Recommended plan applied!"); }
    else showToast("❌ Failed to apply plan","danger");
  });
}

/* ========= Load Expenses ========= */
function loadExpenses(trip_id){
  fetch("expenses_table.php?trip_id="+trip_id)
    .then(res=>res.text())
    .then(html=>{ 
      document.querySelector("#expenses-table-container").innerHTML = html; 
      AOS.refreshHard();
    });
}

/* ========= Edit Fill ========= */
function fillEditForm(id, category, amount, date, notes, image){
  document.getElementById("edit_id").value = id;
  document.getElementById("edit_category").value = category;
  document.getElementById("edit_amount").value = amount;
  document.getElementById("edit_date").value = date;
  document.getElementById("edit_notes").value = notes;
  let preview = document.getElementById("edit_preview");
  if(image){ preview.innerHTML = `<img src="${image}" style="width:80px;height:50px;object-fit:cover;border-radius:6px;">`; }
  else { preview.innerHTML = `<span class="text-muted">No image</span>`; }
}

/* ========= Add Expense ========= */
document.querySelector("form.add-expense-form")?.addEventListener("submit", function(e){
  e.preventDefault();
  let formData = new FormData(this);
  formData.append("action","add");
  fetch("ajax_expenses.php",{ method:"POST", body:formData })
  .then(r=>r.json()).then(d=>{
    if(d.success){ 
      loadExpenses(this.trip_id.value); 
      this.reset(); 
      showToast("✅ Expense added!"); 
    } else {
      showToast("❌ Failed to add expense","danger");
    }
  });
});

/* ========= Delete ========= */
function deleteExpense(id){
  let trip_id = document.getElementById("trip_id_hidden").value;
  if(!confirm("Are you sure?")) return;
  let fd = new FormData(); 
  fd.append("action","delete"); 
  fd.append("id",id); 
  fd.append("trip_id",trip_id);
  fetch("ajax_expenses.php",{ method:"POST", body:fd })
  .then(r=>r.json()).then(d=>{
    if(d.success){ loadExpenses(trip_id); showToast("🗑️ Expense deleted!","warning"); }
    else showToast("❌ Failed to delete","danger");
  });
}

/* ========= Update ========= */
document.querySelector("#editModal form")?.addEventListener("submit", function(e){
  e.preventDefault();
  let fd = new FormData(this);
  fd.append("action","update");
  fd.append("id",document.getElementById("edit_id").value);
  fetch("ajax_expenses.php",{ method:"POST", body:fd })
  .then(r=>r.json()).then(d=>{
    if(d.success){
      loadExpenses(document.getElementById("trip_id_hidden").value);
      let modal = bootstrap.Modal.getInstance(document.getElementById("editModal"));
      modal.hide();
      setTimeout(() => {
        document.body.classList.remove('modal-open');
        document.body.style.overflow = 'auto';
        document.body.style.paddingRight = '';
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
      }, 300);
      showToast("✏️ Expense updated!");
    } else {
      showToast("❌ Failed to update","danger");
    }
  });
});

//  date restrict 
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, '0');
  const dd = String(today.getDate()).padStart(2, '0');

  const formattedToday = `${yyyy}-${mm}-${dd}`;
  document.getElementById("expense-date").setAttribute("min", formattedToday);

  // 🗓️ Restrict edit modal date (today or future)
const editDateInput = document.getElementById("edit_date");
if(editDateInput){
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, '0');
  const dd = String(today.getDate()).padStart(2, '0');
  const formattedToday = `${yyyy}-${mm}-${dd}`;
  editDateInput.setAttribute("min", formattedToday);
}
</script>
</body>
</html>
