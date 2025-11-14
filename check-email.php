<?php
session_start();
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));

    $stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $_SESSION['reset_email'] = $email;
        header("Location: reset-password.php");
        exit();
    } else {
        $_SESSION['error'] = "❌ Email not found!";
        header("Location: forgot_password.php");
        exit();
    }
    $stmt->close();
} else {
    header("Location: forgot_password.php");
    exit();
}
?>
