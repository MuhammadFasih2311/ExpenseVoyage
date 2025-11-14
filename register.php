<?php
session_start();
include 'connect.php'; // DB connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $last_name  = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $email      = mysqli_real_escape_string($conn, trim($_POST['email']));

    $raw_password    = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Server-side validation
    if ($raw_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($raw_password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {
        // Check if email already exists
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            $error = "Email already registered!";
        } else {
            $passwordHash = password_hash($raw_password, PASSWORD_DEFAULT);

            // ✅ currency removed from INSERT
            $sql = "INSERT INTO users (first_name, last_name, email, password) 
                    VALUES ('$first_name', '$last_name', '$email', '$passwordHash')";
            if (mysqli_query($conn, $sql)) {
                $_SESSION['success'] = "Signup successful! Please login.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Create your Expense Voyage account and start booking trips, managing travel expenses, and exploring destinations with ease.">
<title>Sign Up - Expense Voyage</title>
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
    .form-control, .form-select {
      border-radius: 12px; padding: 12px; border: 2px solid transparent;
      box-shadow: 0 3px 10px rgba(0,0,0,0.05); transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
      border: 2px solid #00c6ff; box-shadow: 0 0 12px rgba(0,198,255,0.6);
    }
    .btn-gradient {
      background: linear-gradient(90deg,#007bff,#00c6ff);
      border: none; border-radius: 12px; color: white; padding: 12px;
      font-size: 16px; font-weight: 600; transition: all 0.3s ease;
    }
    .btn-gradient:hover { transform: scale(1.05); box-shadow: 0 6px 20px rgba(0,123,255,0.6); color: white; }
    .card-footer { text-align: center; background: rgba(255,255,255,0.2); padding: 15px; border-top: none; }
    .form-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #007bff; }
    .input-group { position: relative; }
    .input-group input, .input-group select { padding-left: 40px; }
    .alert { border-radius: 12px; }
    .toggle-eye {
      position: absolute; right: 15px; top: 50%; transform: translateY(-50%);
      cursor: pointer; font-size: 18px; color: #6c757d; z-index: 3;
    }
    .pe-5 { padding-right: 3rem !important; } /* space for eye icon */
    .tiny-hint { font-size: 0.85rem; }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5" data-aos="zoom-in">
        <div class="card shadow-lg">
          <div class="card-header">
            <h3><i class="fa-solid fa-user-plus me-2"></i>Create Account</h3>
          </div>
          <div class="card-body p-4">
            <?php if(!empty($error)): ?>
              <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST" novalidate>
              <div class="mb-3 input-group">
                <i class="fa-solid fa-user form-icon"></i>
                <input type="text" name="first_name" class="form-control"
                       placeholder="First Name" required
                       value="<?= isset($first_name)?htmlspecialchars($first_name):'' ?>"
                       minlength="3" maxlength="30" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">
              </div>

              <div class="mb-3 input-group">
                <i class="fa-solid fa-user form-icon"></i>
                <input type="text" name="last_name" class="form-control"
                       placeholder="Last Name" required
                       value="<?= isset($last_name)?htmlspecialchars($last_name):'' ?>" minlength="3" maxlength="30" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">
              </div>

              <div class="mb-3 input-group">
                <i class="fa-solid fa-envelope form-icon"></i>
                <input type="email" name="email" class="form-control"
                       placeholder="Email" required
                       value="<?= isset($email)?htmlspecialchars($email):'' ?>" minlength="4" maxlength="35">
              </div>

              <!-- Password -->
              <div class="mb-1 input-group">
                <i class="fa-solid fa-lock form-icon"></i>
                <input type="password" id="password" name="password"
                       class="form-control pe-5" placeholder="Password (min 8 chars)" required minlength="8" maxlength="30">
                <i class="bi bi-eye toggle-eye" id="togglePassword"></i>
              </div>
              <div class="mb-3">
                <small class="text-light tiny-hint">Tip: Use at least 8 characters.</small>
              </div>

              <!-- Confirm Password -->
              <div class="mb-1 input-group">
                <i class="fa-solid fa-lock form-icon"></i>
                <input type="password" id="confirm_password" name="confirm_password"
                       class="form-control pe-5" placeholder="Confirm Password" required minlength="8" maxlength="30">
                <i class="bi bi-eye toggle-eye" id="toggleConfirm"></i>
              </div>
              <div class="mb-3">
                <small id="matchHint" class="tiny-hint"></small>
              </div>
              
              <button type="submit" id="signupBtn" class="btn btn-gradient w-100" disabled>
                <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Sign Up
              </button>
            </form>
          </div>
          <div class="card-footer">
            <p class="mb-0 text-white">Already have an account?
              <a href="login.php" class="fw-bold text-decoration-none" style="color:#00e1ff;">Login</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
  AOS.init({ duration: 900, easing: 'ease-in-out', once: true });

  // 👁️ Toggle password visibility
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
  attachToggle('password','togglePassword');
  attachToggle('confirm_password','toggleConfirm');

  // 🔍 Field references
  const firstName = document.querySelector('[name="first_name"]');
  const lastName  = document.querySelector('[name="last_name"]');
  const email     = document.querySelector('[name="email"]');
  const pwd       = document.getElementById('password');
  const cpw       = document.getElementById('confirm_password');
  const hint      = document.getElementById('matchHint');
  const btn       = document.getElementById('signupBtn');

  // ✅ Email format regex
  const emailRegex = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/;

  // ✨ Function to validate all required fields
  function validateAllFields() {
    const emailValid = emailRegex.test(email.value.trim());
    const allFilled =
      firstName.value.trim().length >= 3 &&
      lastName.value.trim().length >= 3 &&
      emailValid &&
      pwd.value.length >= 8 &&
      cpw.value.length >= 8 &&
      pwd.value === cpw.value;

    btn.disabled = !allFilled;
  }

  // 🔐 Password match + hint
  function validateMatch(){
    if (!cpw.value) {
      cpw.classList.remove('is-valid','is-invalid');
      hint.textContent = '';
      validateAllFields();
      return;
    }
    const okLen = pwd.value.length >= 8;
    const ok = pwd.value === cpw.value && okLen;

    cpw.classList.toggle('is-valid', ok);
    cpw.classList.toggle('is-invalid', !ok);

    hint.textContent = ok
      ? '✅ Passwords match.'
      : (okLen ? '❌ Passwords do not match.' : '❌ Minimum 8 characters.');

    hint.className = 'tiny-hint ' + (ok ? 'text-success' : 'text-danger');

    validateAllFields();
  }

  // 📩 Email visual feedback
  email.addEventListener('input', () => {
    const valid = emailRegex.test(email.value.trim());
    email.classList.toggle('is-valid', valid);
    email.classList.toggle('is-invalid', !valid && email.value.length > 0);
    validateAllFields();
  });

  // 🎯 Event listeners for live validation
  [firstName, lastName, pwd, cpw].forEach(input => {
    input.addEventListener('input', () => {
      validateMatch();
      validateAllFields();
    });
  });

  // Initial check for autofill
  validateAllFields();
</script>
</body>
</html>
