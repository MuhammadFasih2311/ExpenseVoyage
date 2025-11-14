<?php
require_once "connect.php";

$type = $_GET['type'] ?? '';

$sql = "SELECT u.first_name, u.last_name, u.email, IFNULL(SUM(e.amount),0) AS total_spent
        FROM users u
        LEFT JOIN expenses e ON e.user_id=u.id
        GROUP BY u.id
        ORDER BY total_spent DESC";
$result = $conn->query($sql);

if ($type === 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=report.xls");
    echo "User\tEmail\tTotal Expenses\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['first_name']." ".$row['last_name']."\t".$row['email']."\t".$row['total_spent']."\n";
    }
    exit;
} else {
    echo "Invalid export type.";
}
