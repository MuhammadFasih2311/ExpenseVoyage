<?php
require_once "connect.php";

$type = $_GET['type'] ?? '';

$sql = "SELECT u.first_name, u.last_name, u.email,
               COUNT(b.id) AS total_bookings,
               SUM(CASE WHEN b.payment_status='paid' THEN 1 ELSE 0 END) AS paid,
               SUM(CASE WHEN b.payment_status='unpaid' THEN 1 ELSE 0 END) AS unpaid
        FROM users u
        LEFT JOIN bookings b ON b.user_id = u.id
        GROUP BY u.id
        ORDER BY total_bookings DESC";
$result = $conn->query($sql);

if ($type === 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=bookings-report.xls");
    echo "User\tEmail\tTotal Bookings\tPaid\tUnpaid\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['first_name']." ".$row['last_name']."\t".
             $row['email']."\t".
             $row['total_bookings']."\t".
             $row['paid']."\t".
             $row['unpaid']."\n";
    }
    exit;
} else {
    echo "Invalid export type.";
}
