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
  <h2 class="fw-bold text-gradient mb-4" data-aos="fade-right"><i class="bi bi-graph-up"></i> Expense Reports</h2>

  <!-- 🔹 Total Expenses by User -->
  <div class="card shadow border-0 rounded-3 mb-4" data-aos="zoom-in">
    <div class="card-body">
      <h5 class="mb-3 d-flex justify-content-between align-items-center">
        <span>Total Expenses by User</span>
        <span>
            <a href="export-report.php?type=excel" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
        </span>
        </h5>

      <div class="table-responsive" \>
        <table id="usersReport" class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>User</th>
              <th>Email</th>
              <th>Total Expenses ($)</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $sql = "SELECT 
          u.id, 
          u.first_name, 
          u.last_name, 
          u.email, 
          SUM(e.amount) AS total_spent
        FROM users u
        INNER JOIN expenses e ON e.user_id = u.id
        GROUP BY u.id
        HAVING total_spent > 0
        ORDER BY total_spent DESC";

$res = $conn->query($sql);
$chartData = [];
if ($res && $res->num_rows > 0):
  while($row = $res->fetch_assoc()):
    $fullName = htmlspecialchars($row['first_name'].' '.$row['last_name']);
    $chartData[] = ["name"=>$fullName, "amount"=>$row['total_spent']];
?>
    <tr>
      <td><?= $fullName ?></td>
      <td><?= htmlspecialchars($row['email']) ?></td>
      <td><strong>$<?= number_format($row['total_spent'],2) ?></strong></td>
    </tr>
<?php
  endwhile;
endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- 🔹 Chart Section -->
  <div class="card shadow border-0 rounded-3 mb-4" data-aos="fade-up">
    <div class="card-body">
      <h5 class="mb-3">Top 10 Users (Expenses Overview)</h5>
      <canvas id="userExpenseChart" height="100"></canvas>
    </div>
  </div>

  <!-- 🔹 Trip Breakdown -->
  <div class="card shadow border-0 rounded-3"> 
    <div class="card-body">
      <h5 class="mb-3">Trip Wise Breakdown</h5>
      <div class="table-responsive">
        <table id="tripReport" class="table table-striped table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>User</th>
              <th>Trip</th>
              <th>Total Expenses ($)</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $q = "SELECT u.first_name, u.last_name, t.trip_name, SUM(e.amount) AS total
      FROM expenses e
      INNER JOIN users u ON u.id = e.user_id
      INNER JOIN trips t ON t.id = e.trip_id
      GROUP BY u.id, t.id
      HAVING total > 0
      ORDER BY total DESC";
            $r = $conn->query($q);
            if ($r->num_rows > 0):
              while($row = $r->fetch_assoc()):
                $userName = htmlspecialchars($row['first_name'].' '.$row['last_name']);
                $tripName = htmlspecialchars($row['trip_name']);
            ?>
                <tr>
                  <td><?= $userName ?></td>
                  <td><?= $tripName ?></td>
                  <td><span class="text-success">$<?= number_format($row['total'],2) ?></span></td>
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
  // Chart: Top 10 users
  const chartData = <?= json_encode(array_slice($chartData, 0, 10)) ?>;
  const ctx = document.getElementById('userExpenseChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: chartData.map(d => d.name),
      datasets: [{
        label: 'Total Expenses ($)',
        data: chartData.map(d => d.amount),
        backgroundColor: 'rgba(54, 162, 235, 0.6)',
        borderColor: 'rgba(54, 162, 235, 1)',
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
  $('#usersReport').DataTable({
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50, 100],
    ordering: true,
    searching: true
  });

  $('#tripReport').DataTable({
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50, 100],
    ordering: true,
    searching: true
  });
});
</script>
