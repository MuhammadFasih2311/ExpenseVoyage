<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: admin-login.php"); exit; }

require_once "connect.php";
require_once "inc/admin-header.php";
require_once "inc/admin-sidebar.php";

$user_id = (int)($_GET['user_id'] ?? 0);
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

$trip_filter = $_GET['trip'] ?? '';
$status_filter = $_GET['status'] ?? '';
$payment_filter = $_GET['payment'] ?? '';
$date_filter = $_GET['date'] ?? '';

$where = " WHERE b.user_id=$user_id ";

if($trip_filter != '') $where .= " AND t.trip_name='".$conn->real_escape_string($trip_filter)."' ";
if($status_filter != '') $where .= " AND b.status='".$conn->real_escape_string($status_filter)."' ";
if($payment_filter != '') $where .= " AND b.payment_status='".$conn->real_escape_string($payment_filter)."' ";
if($date_filter != '') $where .= " AND DATE(b.booking_date)='".$conn->real_escape_string($date_filter)."' ";

$sql = "SELECT b.*, t.trip_name 
        FROM bookings b 
        JOIN trips t ON t.id=b.trip_id 
        $where 
        ORDER BY b.id DESC";

$res = $conn->query($sql);
?>

<main class="col-12 col-md-12 col-lg-10 p-4">

<h3 class="fw-bold mb-3 text-gradient d-flex justify-content-between align-items-center">
   <span data-aos="fade-right"><i class="bi bi-journal-text"></i> Booking History of <?= htmlspecialchars($user['first_name']." ".$user['last_name']) ?></span>

   <a href="bookings-reports.php" class="btn btn-info text-light btn-sm" data-aos="fade-left">
      <i class="bi bi-arrow-left"></i> Back
   </a>
</h3>

<!-- 🔹 Filters -->
<form method="get" class="row g-3 mb-3">
    <input type="hidden" name="user_id" value="<?=$user_id?>">

    <div class="col-md-3" data-aos="zoom-in">
        <label class="form-label fw-semibold">Trip Name</label>
        <select name="trip" class="form-select">
            <option value="">All Trips</option>
            <?php
            $t = $conn->query("SELECT DISTINCT t.trip_name FROM bookings b JOIN trips t ON t.id=b.trip_id WHERE b.user_id=$user_id");
            while($x=$t->fetch_assoc()): ?>
              <option value="<?=$x['trip_name']?>" <?=($trip_filter==$x['trip_name'])?'selected':''?>><?=$x['trip_name']?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-3" data-aos="zoom-in" data-aos-delay="100">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select">
            <option value="">All</option>
            <option value="pending" <?=($status_filter=='pending')?'selected':''?>>Pending</option>
            <option value="confirmed" <?=($status_filter=='confirmed')?'selected':''?>>Confirmed</option>
            <option value="cancelled" <?=($status_filter=='cancelled')?'selected':''?>>Cancelled</option>
        </select>
    </div>

    <div class="col-md-3" data-aos="zoom-in" data-aos-delay="200">
        <label class="form-label fw-semibold">Payment Status</label>
        <select name="payment" class="form-select">
            <option value="">All</option>
            <option value="paid" <?=($payment_filter=='paid')?'selected':''?>>Paid</option>
            <option value="unpaid" <?=($payment_filter=='unpaid')?'selected':''?>>Unpaid</option>
            <option value="refunded" <?=($payment_filter=='refunded')?'selected':''?>>Refunded</option>
        </select>
    </div>

    <div class="col-md-3" data-aos="zoom-in" data-aos-delay="300">
        <label class="form-label fw-semibold">Date</label>
        <input type="date" name="date" value="<?=$date_filter?>" class="form-control">
    </div>

    <div class="col-12 text-end">
        <button class="btn btn-primary btn-sm" data-aos="fade-left"><i class="bi bi-funnel"></i> Apply</button>
        <a href="user-booking-history.php?user_id=<?=$user_id?>" class="btn btn-danger btn-sm" data-aos="fade-left" data-aos-delay="100">
          <i class="bi bi-arrow-clockwise"></i> Reset
        </a>
    </div>
</form>

<div class="card shadow rounded-3" data-aos="flip-left">
<div class="table-responsive">
<table id="historyTable" class="table table-hover align-middle">
<thead class="table-light">
<tr>
<th>Trip</th>
<th>Persons</th>
<th>Status</th>
<th>Payment</th>
<th>Booking Date</th>
</tr>
</thead>
<tbody>

<?php while($b=$res->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($b['trip_name']) ?></td>
  <td><?= $b['num_persons'] ?></td>

  <!-- Status -->
  <td>
    <span class="d-none"><?= $b['status'] ?></span>
    <span class="badge bg-<?= $b['status']=='confirmed'?'success':($b['status']=='cancelled'?'danger':'secondary') ?>">
      <?= ucfirst($b['status']) ?>
    </span>
  </td>

  <!-- Payment -->
  <td>
    <span class="d-none"><?= $b['payment_status'] ?></span>
    <span class="badge bg-<?= $b['payment_status']=='paid'?'success':($b['payment_status']=='refunded'?'warning':'dark') ?>">
      <?= ucfirst($b['payment_status']) ?>
    </span>
  </td>

  <!-- Date -->
  <td>
    <span class="d-none"><?= $b['booking_date'] ?></span>
    <?= $b['booking_date'] ?>
  </td>

</tr>
<?php endwhile; ?>

</tbody></table>
</div></div>

</main>
<script>
$(document).ready(function(){

  let table = $('#historyTable').DataTable({
      pageLength: 10,
      ordering: true,
      searching: true
  });

  function applyFilters(){
      let trip = $('#filterTrip').val().toLowerCase();
      let status = $('#filterStatus').val().toLowerCase();
      let payment = $('#filterPayment').val().toLowerCase();
      let date = $('#filterDate').val();

      $.fn.dataTable.ext.search = [];

      $.fn.dataTable.ext.search.push(function(settings, data, dataIndex){
          
          let tripName = data[0].toLowerCase();
          let statusText = $(table.row(dataIndex).node()).find('td:eq(2) span.d-none').text().toLowerCase();
          let paymentText = $(table.row(dataIndex).node()).find('td:eq(3) span.d-none').text().toLowerCase();
          let bookingDate = $(table.row(dataIndex).node()).find('td:eq(4) span.d-none').text();

          if(trip && tripName !== trip) return false;
          if(status && statusText !== status) return false;
          if(payment && paymentText !== payment) return false;
          if(date && bookingDate !== date) return false;

          return true;
      });

      table.draw();
  }

  $('#filterTrip, #filterStatus, #filterPayment, #filterDate').on('change keyup', applyFilters);

  $('#resetFilters').click(function(){
      $('#filterTrip, #filterStatus, #filterPayment, #filterDate').val("");
      $.fn.dataTable.ext.search = [];
      table.draw();
  });

});
</script>


<?php require_once "inc/admin-footer.php"; ?>
