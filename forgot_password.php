<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Recover your Expense Voyage account quickly and securely. Reset your password to continue managing your travel bookings and expenses with ease.">
<title>Forgot Password - Expense Voyage</title>
<link rel="icon" href="images/logo.png" type="image/png">
<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<!-- AOS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />
<!-- FontAwesome & Bootstrap Icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />

<style>
body {
    background: linear-gradient(120deg, #0855ca, #ff7a00);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Segoe UI', sans-serif;
}
.card {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    backdrop-filter: blur(10px);
    background: rgba(255,255,255,0.15);
    box-shadow: 0 8px 30px rgba(0,0,0,0.3);
    animation: floatUp 1s ease forwards;
    opacity: 0;
}
@keyframes floatUp { from { transform: translateY(50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
.card-header {
    background: linear-gradient(90deg, #007bff, #00c6ff);
    color: white; text-align: center; padding: 25px;
}
.card-header h3 { margin: 0; font-weight: bold; }
.form-control {
    border-radius: 12px; padding: 12px 12px 12px 40px; border: 2px solid transparent;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05); transition: all 0.3s ease;
}
.form-control:focus { border-color: #00c6ff; box-shadow: 0 0 12px rgba(0,198,255,0.6); }
.btn-gradient {
    background: linear-gradient(90deg,#007bff,#00c6ff);
    border: none; border-radius: 12px; color: white; padding: 12px; font-size: 16px; font-weight: 600; transition: all 0.3s ease;
}
.btn-gradient:hover { transform: scale(1.05); box-shadow: 0 6px 20px rgba(0,123,255,0.6); }
.card-footer { text-align: center; background: rgba(255,255,255,0.2); padding: 15px; border-top: none; }
.form-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #007bff; }
.input-group { position: relative; }
.input-group input { padding-left: 40px; }
.alert { border-radius: 12px; }
</style>
</head>
<body>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5" data-aos="zoom-in">
      <div class="card shadow-lg">
        <div class="card-header">
          <h3><i class="bi bi-unlock me-2"></i>Forgot Password</h3>
        </div>
        <div class="card-body p-4">
          <?php if(!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
          <?php endif; ?>
          <?php if(!empty($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
          <?php endif; ?>

          <form method="post" action="check-email.php" novalidate>
            <div class="mb-3 input-group">
              <i class="bi bi-envelope form-icon"></i>
              <input type="email" name="email" class="form-control" placeholder="Enter your registered email" required minlength="4" maxlength="30">
            </div>
            <button type="submit" class="btn btn-gradient w-100">
              <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Next
            </button>
          </form>
        </div>
        <div class="card-footer">
            <p class="my-2 text-center "><a href="login.php" class="fw-bold text-decoration-none" style="color:#00e1ff;">← Back to Login</a></p>
          </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>AOS.init({duration:900, easing:'ease-in-out'});</script>
</body>
</html>
