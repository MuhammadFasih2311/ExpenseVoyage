<?php
// Get inputs with prefix
$destination = isset($_GET['home_destination']) ? trim($_GET['home_destination']) : '';
$date        = isset($_GET['home_date']) ? trim($_GET['home_date']) : '';
$budget      = isset($_GET['home_budget']) ? (int)$_GET['home_budget'] : 0;

// Redirect user to trips.php with filters as query string
$query = [];

// filters add karo with prefix
if (!empty($destination)) {
    $query['home_destination'] = $destination;
}
if (!empty($date)) {
    $query['home_date'] = $date;
}
if (!empty($budget)) {
    $query['home_budget'] = $budget;
}

// hamesha page=1 add karo jab search hoti hai
$query['page'] = 1;

$queryString = http_build_query($query);
header("Location: trips.php?$queryString");
exit;
