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
<meta name="description" content="Explore all your planned and past trips with Expense Voyage. Manage bookings, track expenses, and organize your travel adventures easily.">
<title>Trips - Expense Voyage</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
</head>
<style>
    html, body {
  overflow-x: hidden;
}
</style>
<body>
<?php
include 'connect.php';
include 'header.php';

$tripsPerPage = 9;
$page   = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $tripsPerPage;

$where = [];

// ----------- Index.php form filters (via home-fetch.php) -------------
$home_destination = isset($_GET['home_destination']) ? trim($_GET['home_destination']) : '';
$home_date        = isset($_GET['home_date']) ? trim($_GET['home_date']) : '';
$home_budget      = isset($_GET['home_budget']) ? (int)$_GET['home_budget'] : 0;

if (!empty($home_destination)) {
    $dest = $conn->real_escape_string($home_destination);
    $where[] = "destination LIKE '%$dest%'";
}

if (!empty($home_budget)) {
    $minBudget = $home_budget - 250;
    $maxBudget = $home_budget + 250;
    $where[] = "budget BETWEEN $minBudget AND $maxBudget";
}

if (!empty($home_date)) {
    $userDate = date('Y-m-d', strtotime($home_date));
    $minDate = date('Y-m-d', strtotime("$userDate -15 days"));
    $maxDate = date('Y-m-d', strtotime("$userDate +15 days"));
    $where[] = "start_date BETWEEN '$minDate' AND '$maxDate'";
}

// ----------- Trips.php ke apne dropdown + search filters -------------
$searchQuery = '';
$filterDestination = '';

if (!empty($_GET['search'])) {
    $searchQuery = $conn->real_escape_string($_GET['search']);
    $where[] = "(trip_name LIKE '%$searchQuery%' OR destination LIKE '%$searchQuery%')";
}

if (!empty($_GET['category'])) {
    $category = $conn->real_escape_string($_GET['category']);
    $where[] = "category = '$category'";
}

// Trips page dropdown filter: only use if home filter is not present
if (!empty($_GET['destination'])) {
    $filterDestination = $conn->real_escape_string($_GET['destination']);
    $where[] = "destination = '$filterDestination'";
}

// ----------- Final SQL -------------
$whereSql = count($where) ? "WHERE " . implode(" AND ", $where) : "";

$countSql = "SELECT COUNT(*) as total FROM trips $whereSql";
$countResult = $conn->query($countSql);
$totalTrips = ($countResult) ? $countResult->fetch_assoc()['total'] : 0;
$totalPages = max(1, ceil($totalTrips / $tripsPerPage));

$sql = "SELECT * FROM trips $whereSql ORDER BY id DESC LIMIT $offset, $tripsPerPage";
$result = $conn->query($sql);
?>

<section class="page-hero d-flex align-items-center justify-content-center text-center position-relative"
    style="background-image: url('images/trips.jpg'); background-attachment: fixed; background-size: cover; background-position: center; height: 350px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.5);"></div>
    <div class="container position-relative text-white" data-aos="zoom-in">
        <h1 class="fw-bold">Our Trips</h1>
        <p class="lead">Discover exciting destinations & unforgettable journeys</p>
    </div>
</section>

<div class="container py-5">
    <div class="text-center mb-5" data-aos="fade-up">
        <h2 class="fw-bold section-title">Available Trips</h2>
        <p class="text-muted">Explore exciting journeys waiting for you</p>
    </div>

    <!-- Filter & Search -->
    <form id="filterForm" class="mb-4 row justify-content-center g-2" data-aos="fade-up" data-aos-delay="100">
        <div class="col-auto">
            <select name="destination" id="destination" class="form-select">
                <option value="">All Destinations</option>
                <?php
                $destQuery = mysqli_query($conn, "SELECT DISTINCT destination FROM trips ORDER BY destination ASC");
                while ($row = mysqli_fetch_assoc($destQuery)) {
                    $selected = ($filterDestination == $row['destination']) ? "selected" : "";
                    echo "<option value='".htmlspecialchars($row['destination'])."' $selected>".htmlspecialchars($row['destination'])."</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-auto">
            <input type="text" name="search" id="search" class="form-control" placeholder="Search trips..." value="<?= htmlspecialchars($searchQuery); ?>" maxlength="30">
        </div>
    </form>

    <!-- Trips list -->
    <div class="row g-4" id="tripsContainer">
        <?php
        if ($result && mysqli_num_rows($result) > 0) {
            $delay = 0;
            while ($trip = mysqli_fetch_assoc($result)) {
                $imagePath = !empty($trip['image']) ? $trip['image'] : 'images/default.jpg';
                $desc = !empty($trip['description']) ? $trip['description'] : 'No description available.';
                $shortDesc = strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                $delay += 100;
                ?>
                <div class="col-md-4 col-sm-12 mb-4">
                    <div class="trip-card shadow-lg" 
                        data-aos="fade-up" 
                        data-aos-delay="<?= $delay; ?>" 
                        data-aos-duration="700">
                        
                        <div class="trip-image position-relative">
                            <img src="<?= htmlspecialchars($imagePath); ?>" 
                                alt="<?= htmlspecialchars($trip['trip_name']); ?>" 
                                style="width:100%; height:250px; object-fit:cover;">
                            <span class="price-tag text-white px-3 py-1 fw-bold">
                                $<?= number_format($trip['budget'], 2); ?>
                            </span>
                        </div>

                        <div class="trip-info p-3">
                            <h5><?= htmlspecialchars($trip['trip_name']); ?></h5>
                            <p class="text-muted mb-1"><?= htmlspecialchars($trip['destination']); ?></p>
                            <p class="small text-muted mb-2">
                                <?= date("M d, Y", strtotime($trip['start_date'])); ?> - 
                                <?= date("M d, Y", strtotime($trip['end_date'])); ?>
                            </p>
                            <p class="trip-desc"><?= htmlspecialchars($shortDesc); ?></p>
                            <a href="trip-details.php?id=<?= $trip['id']; ?>" 
                                class="btn btn-gradient w-100 mt-2">View Details</a>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p class='text-center' data-aos='fade-up'>No trips found matching your search/filter.</p>";
        }
        ?>
    </div>

    <!-- Pagination -->
    <div id="paginationContainer" class="mt-4 text-center" data-aos="fade-up" data-aos-delay="100">
        <?php
        if ($totalPages > 1) {
            echo '<nav><ul class="pagination justify-content-center">';
            for ($i = 1; $i <= $totalPages; $i++) {
                $active = ($i == $page) ? 'active' : '';
                echo "<li class='page-item $active'><a class='page-link' href='?page=$i' data-page='$i'>$i</a></li>";
            }
            echo '</ul></nav>';
        } else {
            // agar sirf 1 hi page hai tab bhi 1 dikhana hai
            echo '<nav><ul class="pagination justify-content-center">';
            echo "<li class='page-item active'><a class='page-link' href='?page=1' data-page='1'>1</a></li>";
            echo '</ul></nav>';
        }
        ?>
    </div>

</div>

<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
(function () {
  function loadTrips(page, fromHome = false) {
    page = page || 1;
    const search = $("#search").val() || "";
    let destination = $("#destination").val() || "";
    
    // Agar home se aaye ho, toh home=1 bhejo
    const data = { search: search, destination: destination, page: page };
    if (fromHome) {
      data.home = 1;
      data.date = $("#home_date").val() || "";
      data.budget = $("#home_budget").val() || "";
    }

    $.ajax({
      url: "trips_fetch.php",
      type: "GET",
      dataType: "json",
      data: data,
      success: function (res) {
        $("#tripsContainer").html(res.trips);
        $("#paginationContainer").html(res.pagination);

        // Dropdown update: agar destination empty ho, show All Destinations
        if (!destination) {
          $("#destination").val("");
        }

        if (window.AOS && AOS.refresh) AOS.refresh();
      }
    });
  }

  // Trips.php ke apne controls
  $(document).on("keyup", "#search", function () { loadTrips(1); });
  $(document).on("change", "#destination", function () { loadTrips(1); });

  // Pagination intercept
  $(document).on("click", ".pagination a", function (e) {
    if ($(this).data("page")) {
      e.preventDefault();
      const page = parseInt($(this).data("page"), 10) || 1;
      loadTrips(page);
    }
  });

  // Page load pe check karo: agar home se aaye
  $(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('home')) {
      loadTrips(1, true);
    }
  });
})();
</script>

<script>
AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: false,
    mirror: true
});
</script>
</body>
</html>
