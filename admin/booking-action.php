<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { 
  header("Location: admin-login.php"); 
  exit; 
}
require_once "connect.php";

$id = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';
$trip_id = (int)($_GET['trip_id'] ?? 0);
$user_id = (int)($_GET['user_id'] ?? 0);

if ($id > 0) {
  $today = date("Ymd"); // e.g. 20250903
  $rand  = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6)); // random 6-char hex

  switch($action) {
    // ✅ Booking status
    case 'confirm':
      $conn->query("UPDATE bookings SET status='confirmed' WHERE id=$id");
      break;
    case 'pending':
      $conn->query("UPDATE bookings SET status='pending' WHERE id=$id");
      break;
    case 'cancel':
      $conn->query("UPDATE bookings SET status='cancelled' WHERE id=$id");
      break;
    case 'delete':
      $conn->query("DELETE FROM bookings WHERE id=$id");
      break;

    // ✅ Payment status
    case 'paid':
      $txn = "PAY-$today-$rand";
      $conn->query("UPDATE bookings SET payment_status='paid', transaction_id='$txn' WHERE id=$id");
      break;
    case 'unpaid':
      $txn = "CASH-$today-$rand";
      $conn->query("UPDATE bookings SET payment_status='unpaid', transaction_id='$txn' WHERE id=$id");
      break;
    case 'refunded':
      $conn->query("UPDATE bookings SET payment_status='refunded' WHERE id=$id");
      break;
  }
}

header("Location: trips-bookings.php?trip_id=$trip_id&user_id=$user_id");
exit;
