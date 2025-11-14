<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'connect.php';
$user_id = $_SESSION['user_id'];

/* ----------- Quick Stats ----------- */
// 1) Total trips (all admin-added)
$q1 = mysqli_query($conn, "SELECT COUNT(*) as c FROM trips");
$total_trips = mysqli_fetch_assoc($q1)['c'] ?? 0;

// 2) Total bookings (user-specific)
$q2 = mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings WHERE user_id=$user_id");
$total_bookings = mysqli_fetch_assoc($q2)['c'] ?? 0;

// 3) Total expenses (user-specific)
$q3 = mysqli_query($conn, "SELECT SUM(amount) as s FROM expenses WHERE user_id=$user_id");
$total_expenses = mysqli_fetch_assoc($q3)['s'] ?? 0;

// 4) Upcoming trip (nearest start_date)
$q4 = mysqli_query($conn, "SELECT t.trip_name, t.start_date 
                            FROM bookings b
                            JOIN trips t ON b.trip_id = t.id
                            WHERE b.user_id=$user_id 
                            AND t.start_date >= CURDATE() 
                            AND b.status='confirmed'
                            ORDER BY t.start_date ASC LIMIT 1");
$upcoming_trip = mysqli_fetch_assoc($q4);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Access your Expense Voyage dashboard – manage trips, track expenses, and view all your travel bookings in one place.">
<title>Dashboard - Expense Voyage</title>
  <link rel="stylesheet" href="style.css">
  <link rel="icon" href="images/logo.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    html, body {
  overflow-x: hidden;
    }
    body { background:#f8f9fa; }
    .stat-card {
      border-radius: 12px;
      padding:20px;
      background:white;
      box-shadow:0 4px 12px rgba(0,0,0,0.08);
      transition: transform .2s;
    }
    .stat-card:hover { transform: translateY(-4px); }
    .stat-icon {
      font-size:30px;
      padding:15px;
      border-radius:50%;
      color:white;
      margin-bottom:10px;
      display:inline-block;
    }
    .bg-gradient-green { background:linear-gradient(45deg,#20c997,#0dcaf0); }
    .bg-gradient-orange { background:linear-gradient(45deg,#ff7a00,#ffbb33); }
    .bg-gradient-purple { background:linear-gradient(45deg,#6f42c1,#d63384); }
    .bg-gradient-blue { background:linear-gradient(45deg,#0d6efd,#39c0ed); }
  </style>
</head>
<body class="bg-light">
<?php include 'header.php'; ?>

<section class="about-hero d-flex align-items-center justify-content-center text-center position-relative" 
         style="background-image: url('images/dashboard.jpg'); background-attachment: fixed; background-size: cover; background-position: center; height: 350px;">
  <!-- Dark Overlay -->
  <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.5);"></div>
  <!-- Text Content -->
  <div class="container position-relative text-white" data-aos="zoom-in">
   <h1 class="fw-bold">My Dashboard</h1>
    <p>Overview of your trips, bookings & expenses</p>
  </div>
</section>

<div class="container py-5">
  <!-- Stats Row -->
  <div class="row g-4 mb-4">
    <div class="col-md-3" data-aos="zoom-in" data-aos-delay="100">
      <div class="stat-card text-center">
        <div class="stat-icon bg-gradient-green"><i class="bi bi-geo-alt"></i></div>
        <h3><?= $total_trips ?></h3>
        <p class="text-muted mb-0">Total Trips</p>
      </div>
    </div>
    <div class="col-md-3" data-aos="zoom-in" data-aos-delay="200"> 
      <div class="stat-card text-center" >
        <div class="stat-icon bg-gradient-blue"><i class="bi bi-bookmark-check"></i></div>
        <h3><?= $total_bookings ?></h3>
        <p class="text-muted mb-0">My Bookings</p>
      </div>
    </div>
    <div class="col-md-3" data-aos="zoom-in" data-aos-delay="300">
      <div class="stat-card text-center">
        <div class="stat-icon bg-gradient-purple"><i class="bi bi-cash-stack"></i></div>
        <h3>$<?= number_format($total_expenses) ?></h3>
        <p class="text-muted mb-0">My Expenses</p>
      </div>
    </div>
    <div class="col-md-3" data-aos="zoom-in" data-aos-delay="400">
      <div class="stat-card text-center">
        <div class="stat-icon bg-gradient-orange"><i class="bi bi-airplane"></i></div>
        <h6 class="fw-bold mb-1">Upcoming Trip</h6>
        <?php if($upcoming_trip): ?>
          <p class="mb-0"><?= htmlspecialchars($upcoming_trip['trip_name']) ?></p>
          <small class="text-muted"><?= date("M d, Y", strtotime($upcoming_trip['start_date'])) ?></small>
        <?php else: ?>
          <p class="text-muted mb-0">No upcoming trip</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Charts Row -->
  <div class="row g-4 mb-4">
    <div class="col-md-6" data-aos="fade-right">
      <div class="stat-card">
        <h5>Expenses by Category</h5>
        <canvas id="expensesChart"></canvas>
      </div>
    </div>
    <div class="col-md-6">
      <div class="stat-card" data-aos="fade-left">
        <h5>Bookings (Last 6 Months)</h5>
        <canvas id="bookingsChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Recent Trips & Bookings -->
  <div class="row g-4">
    <!-- Recent Trips -->
    <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
      <div class="stat-card">
        <h5 class="mb-3">Recent Trips</h5>
        <?php
        $trips = mysqli_query($conn, "SELECT t.trip_name, t.image, t.start_date 
                                      FROM bookings b
                                      JOIN trips t ON b.trip_id = t.id
                                      WHERE b.user_id=$user_id
                                      ORDER BY b.booking_date DESC LIMIT 3");
        if (mysqli_num_rows($trips)>0) {
          while($t = mysqli_fetch_assoc($trips)) {
            $img = !empty($t['image']) ? $t['image'] : 'images/default.jpg';
            echo "
            <div class='d-flex align-items-center mb-3'>
              <img src='".htmlspecialchars($img)."' class='rounded me-3' style='width:70px;height:50px;object-fit:cover;'>
              <div>
                <h6 class='mb-0'>".htmlspecialchars($t['trip_name'])."</h6>
                <small class='text-muted'>".date("M d, Y", strtotime($t['start_date']))."</small>
              </div>
            </div>";
          }
        } else {
          echo "<p class='text-muted'>No trips booked yet</p>";
        }
        ?>
      </div>
    </div>

    <!-- Recent Bookings -->
    <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
      <div class="stat-card">
        <h5 class="mb-3">Recent Bookings</h5>
        <ul class="list-group list-group-flush">
        <?php
        $books = mysqli_query($conn, "SELECT b.*, t.trip_name 
                                      FROM bookings b
                                      JOIN trips t ON b.trip_id = t.id
                                      WHERE b.user_id=$user_id
                                      ORDER BY b.booking_date DESC LIMIT 5");
        if(mysqli_num_rows($books)>0){
          while($b=mysqli_fetch_assoc($books)){
            $statusClass = ($b['status']=='confirmed')?'success':(($b['status']=='pending')?'warning':'danger');
            echo "<li class='list-group-item d-flex justify-content-between'>
              <span>".htmlspecialchars($b['trip_name'])."</span>
              <span class='badge bg-$statusClass'>".ucfirst($b['status'])."</span>
            </li>";
          }
        } else {
          echo "<li class='list-group-item text-muted'>No bookings yet</li>";
        }
        ?>
        </ul>
      </div>
    </div>

    <!-- Quick Actions -->
  <div class="row text-center mt-5">
    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
      <a href="trips.php" class="btn btn-gradient quick-btn w-100"><i class="bi bi-map"></i> Plan a Trip</a>
    </div>
    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
      <a href="expenses.php" class="btn btn-gradient quick-btn w-100"><i class="bi bi-wallet2"></i> Add Expenses</a>
    </div>
    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
      <a href="my-bookings.php" class="btn btn-gradient quick-btn w-100"><i class="bi bi-journal-bookmark"></i> My Bookings</a>
    </div>
  </div>
</div>

  </div>
</div>



<?php include 'footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
  AOS.init({
    duration: 800, // animation ka time (ms) slow aur smooth ke liye
    easing: 'ease-in-out', // smooth easing
    once: false, // baar-baar animation chalay
  });
</script>
<script>
// Expenses by Category (live user data)
fetch("dashboard_expenses.php")
.then(r=>r.json())
.then(data=>{
  new Chart(document.getElementById("expensesChart"), {
    type:'pie',
    data:{
      labels:data.labels,
      datasets:[{ 
        data:data.amounts,
        backgroundColor:['#0dcaf0','#20c997','#ff7a00','#6f42c1','#198754','#dc3545','#ffc107']
      }]
    }
  });
});

// Bookings per Month
fetch("dashboard_bookings.php")
.then(r=>r.json())
.then(data=>{
  new Chart(document.getElementById("bookingsChart"), {
    type:'bar',
    data:{
      labels:data.labels,
      datasets:[{ 
        label:"Bookings",
        data:data.counts,
        backgroundColor:"#0dcaf0"
      }]
    },
    options:{
      scales:{ y:{ beginAtZero:true } }
    }
  });
});
</script>
</body>
</html>

