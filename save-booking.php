<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'connect.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $trip_id = intval($_POST['trip_id']);
    $num_persons = intval($_POST['num_persons']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $payment_method = $_POST['payment_method']; // "cash" ya "online"
    $transaction_input = isset($_POST['transaction_id']) ? trim($_POST['transaction_id']) : "";

    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

// ✅ Phone must be 11 digits
if (!preg_match("/^[0-9]{11}$/", $phone)) {
    die("⚠️ Invalid Phone Number! Must be 11 digits.");
}

// ✅ Last 1 hour booking limit (max 3)
$check = mysqli_query($conn, 
"SELECT COUNT(*) AS total FROM bookings 
 WHERE user_id=$user_id 
 AND booking_date >= (NOW() - INTERVAL 1 HOUR)");

$data = mysqli_fetch_assoc($check);

if ($data['total'] >= 3) {
    die("⛔ You can only book **3 trips per hour**. Try again later.");
}


    // ✅ Server-side validation (1–9 persons allowed)
    if (!($trip_id > 0 && $num_persons >= 1 && $num_persons <= 9)) {
        die("⚠️ Invalid number of persons. Only 1 to 9 members are allowed.");
    }

    // ✅ Helper function to generate IDs
    function generateId($prefix) {
        return $prefix . "-" . date("Ymd") . "-" . strtoupper(substr(md5(uniqid()), 0, 6));
    }

    // ✅ Decide payment + transaction ID
    if ($payment_method === "online") {
        $payment_status = "paid";
        $transaction_id = !empty($transaction_input) ? mysqli_real_escape_string($conn, $transaction_input) : generateId("PAY");
    } else {
        $payment_status = "unpaid";
        $transaction_id = generateId("CASH");
    }
    // 🚀 Future Payment Gateway Integration Point
    // Example flow:
    // if ($payment_status == "paid") {
    //     1. Verify payment response from PayPal/Stripe/JazzCash API
    //     2. Get $transaction_id from gateway response
    //     3. Set $payment_status = "paid" only if verification succeeds
    // }

    // ✅ Insert booking
    $sql = "INSERT INTO bookings (user_id, trip_id, num_persons, phone, status, payment_status, transaction_id, notes)
            VALUES ($user_id, $trip_id, $num_persons, '$phone', 'pending', '$payment_status', '$transaction_id', '$notes')";

    if (mysqli_query($conn, $sql)) {
        header("Location: my-bookings.php?success=1");
        exit();
    } else {
        die("Database Error: " . mysqli_error($conn));
    }
}
?>
