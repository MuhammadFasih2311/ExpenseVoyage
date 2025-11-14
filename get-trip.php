<?php
session_start();
if (!isset($_SESSION['user_id'])) { exit("Login required."); }
include 'connect.php';

if (!isset($_GET['trip_id']) || !is_numeric($_GET['trip_id'])) {
    exit("<p class='text-danger'>Invalid Trip.</p>");
}

$trip_id = intval($_GET['trip_id']);
$trip_sql = "SELECT * FROM trips WHERE id = $trip_id";
$result = mysqli_query($conn, $trip_sql);

if (!$result || mysqli_num_rows($result) == 0) {
    exit("<p class='text-danger'>Trip not found.</p>");
}
$trip = mysqli_fetch_assoc($result);
?>

<!-- Trip Summary -->
<div class="card shadow-sm mb-4">
  <?php if (!empty($trip['image'])): ?>
    <img src="<?= htmlspecialchars($trip['image']); ?>" class="card-img-top" style="height:450px; object-fit:cover;" data-aos="fade-up">
  <?php endif; ?>
  <div class="card-body">
    <h4 class="fw-bold" data-aos="fade-right" data-aos-delay="100"><?= htmlspecialchars($trip['trip_name']); ?></h4>
    <p class="mb-1" data-aos="fade-right" data-aos-delay="300"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($trip['destination']); ?></p>
    <p class="small text-muted mb-1" data-aos="fade-right" data-aos-delay="500">
      <?= date("M d, Y", strtotime($trip['start_date'])); ?> -
      <?= date("M d, Y", strtotime($trip['end_date'])); ?>
    </p>
    <h5 class="text-primary" data-aos="fade-left" data-aos-delay="600">Budget: $<?= number_format($trip['budget']); ?></h5>
  </div>
</div>

<!-- Booking Form -->
<form method="POST" action="save-booking.php">
  <input type="hidden" name="trip_id" value="<?= $trip['id']; ?>">
  <div class="row g-3">
    <div class="col-md-6" data-aos="fade-left">
      <label class="form-label fw-bold">Number of Persons</label>
      <input type="number" class="form-control" name="num_persons" id="num_persons"
       value="1" min="1" max="9" required>
    </div>
    <div class="col-md-6" data-aos="fade-right">
      <label class="form-label fw-bold">Special Requests</label>
      <input type="text" class="form-control" name="notes" placeholder="Any notes or requirements?" maxlength="120">
    </div>
    <div class="col-md-6" data-aos="fade-left">
      <label class="form-label fw-bold">Payment Method</label>
      <select class="form-select" name="payment_method" id="payment_method" required>
      <option value="cash">Cash</option>
      <option value="online">Online / Card</option>
    </select>
    </div>
    <div class="col-md-6" data-aos="fade-left">
  <label class="form-label fw-bold">Phone Number</label>
  <input type="text" class="form-control" name="phone" placeholder="03xxxxxxxxx" 
    maxlength="11" required 
    oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)">
  <small class="text-muted">Must be 11 digits.</small>
</div>
    <div class="col-md-6 d-none" id="transaction_field">
      <label class="form-label fw-bold">Transaction ID / Reference</label>
      <input type="text" class="form-control" name="transaction_id" placeholder="Enter transaction/reference number" maxlength="11">
    </div>
    <!-- 🚀 Future: Yahan Payment Gateway button aayega (PayPal / Stripe / JazzCash) -->
<!-- Example: <div id="paypal-button-container"></div> -->
  </div>
  <button type="submit" class="btn btn-gradient w-100 mt-4" data-aos="zoom-in" data-aos-delay="100">Confirm Booking</button>
</form>

<script>
document.getElementById("payment_method").addEventListener("change", function() {
  let field = document.getElementById("transaction_field");
  let input = field.querySelector("input");
  if (this.value === "paid") {
    field.classList.remove("d-none");
    input.required = true;
  } else {
    field.classList.add("d-none");
    input.required = false;
    input.value = "";
  }
});

</script>

<script>
const numInput = document.getElementById('num_persons');

// Input validation (typing)
numInput.addEventListener('input', function () {
  // Remove non-digits
  this.value = this.value.replace(/[^0-9]/g, '');

  // Allow blank (for backspace) but block multiple digits
  if (this.value.length > 1) {
    this.value = this.value.charAt(0);
  }

  // Enforce range (1–9) only if not empty
  if (this.value !== '') {
    let val = parseInt(this.value, 10);
    if (val < 1) this.value = 1;
    if (val > 9) this.value = 9;
  }
});

// Agar user blur kare aur input blank ho, to 1 set kar do
numInput.addEventListener('blur', function () {
  if (this.value === '') {
    this.value = 1;
  }
});
</script>