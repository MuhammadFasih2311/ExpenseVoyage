<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: admin-login.php");
  exit;
}
require_once "connect.php";
require_once "inc/admin-header.php";
require_once "inc/admin-sidebar.php";
?>

<main class="col-12 col-md-12 col-lg-10 p-4">
  <h2 class="fw-bold text-gradient mb-4" data-aos="fade-right"><i class="bi bi-graph-up"></i> Bookings Reports</h2>

  <!-- 🔹 Total Bookings by User -->
  <div class="card shadow border-0 rounded-3 mb-4" data-aos="zoom-in">
    <div class="card-body">
      <h5 class="mb-3 d-flex justify-content-between align-items-center">
        <span>Total Bookings by User</span>
        <span>
            <a href="bookings-export.php?type=excel" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
        </span>
        </h5>

      <div class="table-responsive">
        <table id="usersReport" class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>User</th>
              <th>Email</th>
              <th>Total Bookings</th>
              <th>Paid</th>
              <th>Unpaid</th>
            </tr>
          </thead>
          <tbody>
          <?php
         $sql = "SELECT 
          u.id, 
          u.first_name, 
          u.last_name, 
          u.email, 
          COUNT(b.id) AS total_bookings,
          SUM(CASE WHEN b.payment_status='paid' THEN 1 ELSE 0 END) AS paid,
          SUM(CASE WHEN b.payment_status='unpaid' THEN 1 ELSE 0 END) AS unpaid
        FROM users u
        INNER JOIN bookings b ON b.user_id = u.id
        GROUP BY u.id
        HAVING total_bookings > 0
        ORDER BY total_bookings DESC";
          $res = $conn->query($sql);
          $chartData = [];
          if ($res->num_rows > 0):
            while($row = $res->fetch_assoc()):
              $fullName = htmlspecialchars($row['first_name'].' '.$row['last_name']);
              $chartData[] = ["name"=>$fullName, "bookings"=>$row['total_bookings']];
          ?>
              <tr>
                <td><?= $fullName ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><strong><?= $row['total_bookings'] ?></strong></td>
                <td><span class="badge bg-success"><?= $row['paid'] ?></span></td>
                <td><span class="badge bg-danger"><?= $row['unpaid'] ?></span></td>
              </tr>
          <?php endwhile; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- 🔹 Chart Section -->
  <div class="card shadow border-0 rounded-3 mb-4" data-aos="fade-up">
    <div class="card-body">
      <h5 class="mb-3">Top 10 Users (Bookings Overview)</h5>
      <canvas id="userBookingChart" height="100"></canvas>
    </div>
  </div>

  <!-- 🔹 Trip Wise Breakdown -->
  <div class="card shadow border-0 rounded-3"> 
    <div class="card-body">
      <h5 class="mb-3">Trip Wise Breakdown</h5>
      <div class="table-responsive">
        <table id="tripReport" class="table table-striped table-hover align-middle">
  <thead class="table-light">
    <tr>
      <th>User</th>
      <th>Total Bookings</th>
      <th>View</th>
    </tr>
  </thead>
  <tbody>
  <?php
  $q = "SELECT 
          u.id AS user_id,
          u.first_name,
          u.last_name,
          COUNT(b.id) AS total_bookings
        FROM bookings b
        JOIN users u ON u.id = b.user_id
        GROUP BY u.id
        HAVING total_bookings > 0
        ORDER BY total_bookings DESC";

  $r = $conn->query($q);
  if ($r->num_rows > 0):
    while($row = $r->fetch_assoc()):
      $userName = htmlspecialchars($row['first_name'].' '.$row['last_name']);
  ?>
    <tr>
      <td><?= $userName ?></td>
      <td><strong><?= $row['total_bookings'] ?></strong></td>
      <td>
        <a href="user-booking-history.php?user_id=<?= $row['user_id'] ?>" 
           class="btn btn-sm btn-info">
          <i class="bi bi-eye"></i> View
        </a>
      </td>
    </tr>
  <?php endwhile; endif; ?>
  </tbody>
</table>
      </div>
    </div>
  </div>
</main>

<?php require_once "inc/admin-footer.php"; ?>

<!-- 🔹 Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const chartData = <?= json_encode(array_slice($chartData, 0, 10)) ?>;
  const ctx = document.getElementById('userBookingChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: chartData.map(d => d.name),
      datasets: [{
        label: 'Total Bookings',
        data: chartData.map(d => d.bookings),
        backgroundColor: 'rgba(75, 192, 192, 0.6)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } }
    }
  });
</script>

<!-- 🔹 DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function(){
  $('#usersReport').DataTable();
  $('#tripReport').DataTable();
});
</script>
