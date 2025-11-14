<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'connect.php';

$user_id = $_SESSION['user_id'];

// Fetch trips for dropdown
$trips = mysqli_query($conn, "SELECT id, trip_name, destination FROM trips ORDER BY trip_name ASC");

$error_msg = "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Book your dream trip with Expense Voyage – easy travel planning, secure bookings, and smart expense management for a stress-free journey.">
<title>Book Trip - Expense Voyage</title>

  <link rel="icon" href="images/logo.png" type="image/png">
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    html, body {
  overflow-x: hidden;
}
    body { background: #f8f9fa; }
    .booking-card {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    .booking-header {
      background: linear-gradient(45deg, #20c997, #0dcaf0);
      color: #fff;
      padding: 20px;
      text-align: center;
    }
    .btn-gradient {
      background: linear-gradient(45deg, #20c997, #0dcaf0);
      border: none;
      color: #fff;
      font-weight: 500;
      padding: 10px 20px;
      border-radius: 30px;
      transition: all 0.3s ease;
    }
    .btn-gradient:hover {
      background: linear-gradient(45deg, #0dcaf0, #20c997);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
  </style>
</head>
<body>

<?php include 'header.php'; ?>

<!-- ✅ Hero Section -->
<section class="page-hero d-flex flex-column align-items-center justify-content-center text-center position-relative"
  style="background-image: url('images/bookings.jpg'); background-attachment: fixed; background-size: cover; background-position: center; height: 350px;">
  <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.5);"></div>
  <div class="container position-relative text-white" data-aos="zoom-in">
    <h1 class="fw-bold">Book Your Adventure</h1>
    <p class="lead mb-4">Secure your spot and start your journey today</p>
    <a href="my-bookings.php" class="btn btn-gradient">
      <i class="bi bi-journal-check"></i> My Bookings
    </a>
  </div>
</section>

<div class="container py-5">
  <div class="booking-card" data-aos="zoom-in">
    <div class="booking-header">
      <h2 class="fw-bold mb-0">Book Your Trip</h2>
    </div>
    <div class="p-4">

      <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger"><?= $error_msg; ?></div>
      <?php endif; ?>

      <!-- Trip Selection -->
      <div class="mb-4">
        <label for="trip_id" class="form-label fw-bold">Select a Trip</label>
        <select name="trip_id" id="trip_id" class="form-select">
          <option value="0">-- Choose a Trip --</option>
          <?php mysqli_data_seek($trips, 0); while ($row = mysqli_fetch_assoc($trips)): ?>
            <option value="<?= $row['id']; ?>">
              <?= htmlspecialchars($row['trip_name']); ?> (<?= htmlspecialchars($row['destination']); ?>)
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Trip Summary (Ajax se load hoga) -->
      <div id="trip-summary" class="text-muted text-center">
        Please select a trip to continue booking.
      </div>

    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
AOS.init({ duration: 800, easing: 'ease-in-out' });

// 🧠 Helper function to get trip_id from URL
function getUrlTrip() {
  const params = new URLSearchParams(window.location.search);
  return params.get("trip_id");
}

// 🧩 Function to load trip summary
function loadTripSummary(tripId) {
  if (tripId > 0) {
    fetch("get-trip.php?trip_id=" + tripId)
      .then(res => res.text())
      .then(data => {
        document.getElementById("trip-summary").innerHTML = data;
      });
  } else {
    document.getElementById("trip-summary").innerHTML = 
      "<p class='text-muted text-center'>Please select a trip to continue booking.</p>";
  }
}

// 🧩 Handle dropdown change normally
document.getElementById("trip_id").addEventListener("change", function() {
  loadTripSummary(this.value);
});

// 🧩 Auto-select trip if trip_id is present in URL
window.addEventListener("DOMContentLoaded", () => {
  const tripId = getUrlTrip();
  const select = document.getElementById("trip_id");
  
  if (tripId) {
    select.value = tripId; // auto-select in dropdown
    loadTripSummary(tripId); // auto-load trip summary
  }
});
</script>
</body>
</html>


