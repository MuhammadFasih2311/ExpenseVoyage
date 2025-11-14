<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: admin-login.php");
  exit;
}
$active = basename($_SERVER['PHP_SELF']); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Access the Expense Voyage Admin Panel – manage users, trips, bookings, and expenses efficiently with powerful admin tools.">
<title>ExpenseVoyage Admin</title>
  <link rel="icon" href="../images/logo.png" type="image/png">
  <link rel="stylesheet" href="assets/admin.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- AOS Animation -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <!-- Font Awesome 6 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

  <style>
    :root {
      --primary:#0d6efd; 
      --accent:#fd7e14; 
      --dark:#0b1020;
      --light:#f8f9fa;
    }
    body { background:#0a0f1d; color:#e9eef7; font-family:"Segoe UI",sans-serif; }

    /* 🔹 Topbar */
    .topbar { background:linear-gradient(90deg, var(--primary), var(--accent)); }
    .topbar .navbar-brand { font-weight:700; color:#fff; }
    .topbar .btn { border-radius:8px; }

    /* Responsive sidebar toggle btn */
    .sidebar-toggle {
      display:none;
      background:none;
      border:0;
      font-size:1.5rem;
      color:#fff;
    }
    @media(max-width:991px){
      .sidebar-toggle { display:block; }
    }

  </style>
</head>
<body>
 <!-- 🔹 Top Navbar -->
<nav class="navbar fixed-top topbar navbar-dark shadow-sm" data-aos="fade-down">
  <div class="container-fluid d-flex justify-content-between align-items-center">

    <!-- ☰ button  --> 
    <button class="btn btn-light d-lg-none position-absolute start-0 ms-2" id="openSidebarBtn">
      <i class="bi bi-list fs-4"></i>
    </button>

    <!-- Brand always center -->
    <a class="navbar-brand mx-auto fw-bold">
      <img src="..\images\logo.png" alt="" width="50px"><span style="color:#ff7a00">Expense</span>Voyage<span style="color:#0d6efd;"> Admin</span></a>
  </div>
</nav>
<br><br><br>
<div class="container-fluid d-flex flex-column min-vh-100">
  <div class="row flex-grow-1">
