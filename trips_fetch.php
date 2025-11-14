<?php
include 'connect.php';
session_start();

$tripsPerPage = 9;
$page = (isset($_GET['page']) && is_numeric($_GET['page']) && (int)$_GET['page'] > 0) ? (int)$_GET['page'] : 1;

// Trips.php ke apne filters
$searchQuery       = isset($_GET['search']) ? trim($_GET['search']) : "";
$filterDestination = isset($_GET['destination']) ? trim($_GET['destination']) : "";

// Home page filters store karna
if (isset($_GET['home'])) {
    $_SESSION['home_destination'] = $filterDestination;
    $_SESSION['home_date']        = isset($_GET['date']) ? trim($_GET['date']) : '';
    $_SESSION['home_budget']      = isset($_GET['budget']) ? (int)$_GET['budget'] : 0;
}

// Agar All Destinations select kiya to home session clear kar do
if ($filterDestination === "") {
    unset($_SESSION['home_destination']);
    unset($_SESSION['home_date']);
    unset($_SESSION['home_budget']);
}

// All Destinations select hone par session clear
if ($filterDestination === "") {
    unset($_SESSION['home_destination'], $_SESSION['home_date'], $_SESSION['home_budget']);
}
// Agar trips.php ke filters empty hain aur home session available hai, apply karo
$homeDate   = '';
$homeBudget = 0;
if (!$searchQuery && !$filterDestination && isset($_SESSION['home_destination'])) {
    $filterDestination = $_SESSION['home_destination'];
    $homeDate = $_SESSION['home_date'];
    $homeBudget = $_SESSION['home_budget'];
}

$where = [];

// Trips.php ke filters
if ($filterDestination !== "" && $searchQuery !== "") {
    $safeSearch = mysqli_real_escape_string($conn, $searchQuery);
    $safeDest   = mysqli_real_escape_string($conn, $filterDestination);
    $where[] = "(destination = '$safeDest' OR trip_name LIKE '%$safeSearch%' OR destination LIKE '%$safeSearch%')";
} elseif ($filterDestination !== "") {
    $where[] = "destination = '" . mysqli_real_escape_string($conn, $filterDestination) . "'";
} elseif ($searchQuery !== "") {
    $safeSearch = mysqli_real_escape_string($conn, $searchQuery);
    $where[] = "(trip_name LIKE '%$safeSearch%' OR destination LIKE '%$safeSearch%')";
} else {
    // Agar dropdown aur search blank ho, home session se filters apply karo
    if ($homeDate) {
        $userDate = date('Y-m-d', strtotime($homeDate));
        $minDate = date('Y-m-d', strtotime("$userDate -15 days"));
        $maxDate = date('Y-m-d', strtotime("$userDate +15 days"));
        $where[] = "start_date BETWEEN '$minDate' AND '$maxDate'";
    }
    if ($homeBudget) {
        $minBudget = $homeBudget - 250;
        $maxBudget = $homeBudget + 250;
        $where[] = "budget BETWEEN $minBudget AND $maxBudget";
    }
}

$whereSql = $where ? " WHERE " . implode(" AND ", $where) : "";

// Count total trips
$countSql = "SELECT COUNT(*) AS total FROM trips $whereSql";
$countResult = mysqli_query($conn, $countSql);
$totalTrips = ($countResult) ? (int)mysqli_fetch_assoc($countResult)['total'] : 0;

$totalPages = max(1, (int)ceil($totalTrips / $tripsPerPage));
if ($page > $totalPages) { $page = $totalPages; }
$offset = ($page - 1) * $tripsPerPage;

// Fetch trips
$sql = "SELECT * FROM trips $whereSql ORDER BY id DESC LIMIT $offset, $tripsPerPage";
$result = mysqli_query($conn, $sql);

// Build trips HTML
$tripsHtml = "";
if ($result && mysqli_num_rows($result) > 0) {
    $delay = 0;
    while ($trip = mysqli_fetch_assoc($result)) {
        $imagePath = !empty($trip['image']) ? $trip['image'] : 'images/default.jpg';
        $desc      = !empty($trip['description']) ? $trip['description'] : 'No description available.';
        $shortDesc = (strlen($desc) > 100) ? substr($desc, 0, 100) . '...' : $desc;
        $delay += 100;

        $tripsHtml .= '
        <div class="col-md-4 col-sm-12 mb-4" 
             data-aos="fade-up" 
             data-aos-delay="'.$delay.'" 
             data-aos-duration="800">
            <div class="trip-card shadow-lg">
                <div class="trip-image position-relative">
                    <img src="'.htmlspecialchars($imagePath).'" 
                         alt="'.htmlspecialchars($trip['trip_name']).'" 
                         style="width:100%; height:250px; object-fit:cover;">
                    <span class="price-tag text-white px-3 py-1 fw-bold">
                        $'.number_format((float)$trip['budget'], 2).'
                    </span>
                </div>
                <div class="trip-info p-3">
                    <h5>'.htmlspecialchars($trip['trip_name']).'</h5>
                    <p class="text-muted mb-1">'.htmlspecialchars($trip['destination']).'</p>
                    <p class="small text-muted mb-2">
                        '.date("M d, Y", strtotime($trip['start_date'])).' - '.date("M d, Y", strtotime($trip['end_date'])).'
                    </p>
                    <p class="trip-desc">'.htmlspecialchars($shortDesc).'</p>
                    <a href="trip-details.php?id='.(int)$trip['id'].'" 
                       class="btn btn-gradient w-100 mt-2">View Details</a>
                </div>
            </div>
        </div>';
    }
} else {
    $tripsHtml = "<p class='text-center'>No trips found.</p>";
}

// Build pagination HTML
$paginationHtml = '<ul class="pagination justify-content-center">';
if ($page > 1) {
    $paginationHtml .= '<li class="page-item"><a class="page-link" href="#" data-ajax="1" data-page="'.($page - 1).'">Previous</a></li>';
}
for ($i = 1; $i <= $totalPages; $i++) {
    $active = ($i == $page) ? 'active' : '';
    $paginationHtml .= '<li class="page-item '.$active.'"><a class="page-link" href="#" data-ajax="1" data-page="'.$i.'">'.$i.'</a></li>';
}
if ($page < $totalPages) {
    $paginationHtml .= '<li class="page-item"><a class="page-link" href="#" data-ajax="1" data-page="'.($page + 1).'">Next</a></li>';
}
$paginationHtml .= '</ul>';

header('Content-Type: application/json');
echo json_encode([
    "trips" => $tripsHtml,
    "pagination" => $paginationHtml
]);
