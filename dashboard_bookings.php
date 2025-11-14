<?php
session_start();
include 'connect.php';
$user_id = $_SESSION['user_id'];

$res = mysqli_query($conn,"SELECT DATE_FORMAT(booking_date,'%b %Y') as month, COUNT(*) as c
                           FROM bookings
                           WHERE user_id=$user_id 
                           AND booking_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                           GROUP BY month ORDER BY booking_date");
$labels=[]; $counts=[];
while($r=mysqli_fetch_assoc($res)){
  $labels[]=$r['month'];
  $counts[]=$r['c'];
}
echo json_encode(["labels"=>$labels,"counts"=>$counts]);
