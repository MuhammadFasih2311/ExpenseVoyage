<?php
session_start();
include 'connect.php'; 

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot-password.php");
    exit();
}

$error = "";
$success = "";
$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password !== $confirm_password) {
        $error = "❌ Passwords do not match.";
    } elseif (strlen($new_password) < 8) {
        $error = "❌ Password must be at least 8 characters.";
    } else {
        // Fetch old password from DB
        $stmt = $conn->prepare("SELECT password FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($old_hashed);
        $stmt->fetch();
        $stmt->close();

        if ($old_hashed && password_verify($new_password, $old_hashed)) {
            // Same as old password
            $error = "⚠️ New password cannot be the same as your old password.";
        } else {
            // Hash new password
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
            $stmt->bind_param("ss", $hashed, $email);

            if ($stmt->execute()) {
                unset($_SESSION['reset_email']);
                $_SESSION['success'] = "✅ Password updated successfully! Please login.";
                header("Location: login.php");
                exit();
            } else {
                $error = "❌ Error updating password. Please try again.";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Reset your Expense Voyage account password securely and regain access to your travel bookings and expense management tools.">
<title>Reset Password - Expense Voyage</title>
  <link rel="icon" href="images/logo.png" type="image/png">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- AOS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
  <!-- FontAwesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

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
    .form-control { border-radius: 12px; padding: 12px; border: 2px solid transparent; transition: all 0.3s ease; }
    .form-control:focus { border: 2px solid #00c6ff; box-shadow: 0 0 12px rgba(0,198,255,0.6); }
    .btn-gradient {
      background: linear-gradient(90deg,#007bff,#00c6ff);
      border: none; border-radius: 12px; color: white; padding: 12px;
      font-size: 16px; font-weight: 600; transition: all 0.3s ease;
    }
    .btn-gradient:hover { transform: scale(1.05); box-shadow: 0 6px 20px rgba(0,123,255,0.6); color: white; }
    .form-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #007bff; }
    .input-group { position: relative; margin-bottom: 1rem; }
    .input-group input { padding-left: 40px; padding-right: 45px; }
    .toggle-eye { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 18px; color: #6c757d; }
    .alert { border-radius: 12px; }
    .tiny-hint { font-size: 0.85rem; }
    .card-footer { text-align: center; background: rgba(255,255,255,0.2); padding: 15px; border-top: none; }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5" data-aos="zoom-in">
        <div class="card shadow-lg">
          <div class="card-header">
            <h3><i class="fa-solid fa-key me-2"></i>Reset Password</h3>
          </div>
          <div class="card-body p-4">
            <?php if(!empty($error)): ?>
              <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST" novalidate>
              <div class="input-group">
                <i class="fa-solid fa-lock form-icon"></i>
                <input type="password" id="new_password" name="new_password" class="form-control" placeholder="New Password (min 8 chars)" required minlength="8" maxlength="">
                <i class="bi bi-eye toggle-eye" id="eyeNew"></i>
              </div>
              <div class="input-group">
                <i class="fa-solid fa-lock form-icon"></i>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm Password" required minlength="8">
                <i class="bi bi-eye toggle-eye" id="eyeConfirm"></i>
              </div>
              <div id="matchHint" class="tiny-hint mb-3"></div>
              <button type="submit" id="resetBtn" class="btn btn-gradient w-100" disabled>Update Password</button>
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
  <script>
    AOS.init({ duration: 900, easing: 'ease-in-out', once: true });

    function attachToggle(inputId, iconId){
      const input = document.getElementById(inputId);
      const icon  = document.getElementById(iconId);
      icon.addEventListener('click', function () {
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');
      });
    }
    attachToggle('new_password','eyeNew');
    attachToggle('confirm_password','eyeConfirm');

    const pwd = document.getElementById('new_password');
    const cpw = document.getElementById('confirm_password');
    const hint = document.getElementById('matchHint');
    const btn = document.getElementById('resetBtn');

    function validateMatch(){
      if (!cpw.value) {
        cpw.classList.remove('is-valid','is-invalid'); hint.textContent = ''; btn.disabled=true; return;
      }
      const okLen = pwd.value.length >= 8;
      const ok = pwd.value === cpw.value && okLen;

      cpw.classList.toggle('is-valid', ok);
      cpw.classList.toggle('is-invalid', !ok);

      hint.textContent = ok ? '✅ Passwords match.' : (okLen ? '❌ Passwords do not match.' : '❌ Minimum 8 characters.');
      hint.className = 'tiny-hint ' + (ok ? 'text-success' : 'text-danger');
      btn.disabled = !ok;
    }

    pwd.addEventListener('input', validateMatch);
    cpw.addEventListener('input', validateMatch);
  </script>
</body>
</html>
